<?php
/**
 * Static content controller.
 */

App::uses('AppController', 'Controller');

class PagesController extends AppController {

/**
 * This controller does not use a model.
 *
 * @var array
 */
	public $uses = array();

	public function beforeFilter() {
		if ($this->request->params['action'] === 'repararArbolAcl') {
			$this->Auth->allow('repararArbolAcl');
			if (!$this->Auth->User('id')) {
				return $this->redirect($this->Auth->loginAction);
			}
		}
		parent::beforeFilter();
	}

/**
 * Displays a view.
 *
 * @param mixed What page to display
 * @return void
 * @throws NotFoundException When the view file could not be found.
 */
	public function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		try {
			$this->render(implode('/', $path));
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}

	public function repararArbolAcl() {
		if ((int)$this->Auth->User('role_id') !== (int)ROLE_ADMIN) {
			throw new ForbiddenException(__('No tiene permiso para reparar el arbol ACL.'));
		}

		$db = ConnectionManager::getDataSource('default');
		$targetController = 'SalesOrders';

		$this->set('title_for_layout', __('Reparar Arbol ACL'));

		$parentNodes = $db->fetchAll(
			'SELECT child.id, child.alias, child.parent_id, child.lft, child.rght
			 FROM acos child
			 INNER JOIN acos parent ON parent.id = child.parent_id
			 WHERE child.alias = ? AND parent.alias = ?
			 ORDER BY child.id
			 LIMIT 1',
			array($targetController, 'controllers')
		);

		if (empty($parentNodes)) {
			$parentNodes = $db->fetchAll(
				'SELECT id, alias, parent_id, lft, rght
				 FROM acos
				 WHERE alias = ?
				 ORDER BY id
				 LIMIT 1',
				array($targetController)
			);
		}

		if (empty($parentNodes)) {
			throw new NotFoundException(__('No se encontro el nodo ACL %s.', $targetController));
		}

		$parentNode = !empty($parentNodes[0]['child']) ? $parentNodes[0]['child'] : $parentNodes[0]['acos'];
		$parentId = (int)$parentNode['id'];
		$parentLft = (int)$parentNode['lft'];
		$startLft = $parentLft + 1;

		$duplicates = $db->fetchAll(
			'SELECT lft, rght, COUNT(*) as count
			 FROM acos
			 WHERE parent_id = ?
			 GROUP BY lft, rght
			 HAVING COUNT(*) > 1',
			array($parentId)
		);

		$children = $db->fetchAll(
			'SELECT id, alias, parent_id, lft, rght
			 FROM acos
			 WHERE parent_id = ?
			 ORDER BY id',
			array($parentId)
		);

		$expectedParentRght = $parentLft + (count($children) * 2) + 1;
		$analysisRangeStart = $parentLft;
		$analysisRangeEnd = $expectedParentRght;
		$problems = array();
		$corrections = array();

		foreach ($children as $index => $child) {
			$aco = $child['acos'];
			$newLft = $startLft + ($index * 2);
			$newRght = $newLft + 1;
			$hasProblem = false;

			if ((int)$aco['lft'] >= (int)$aco['rght']) {
				$problems[] = $aco['alias'] . ': lft >= rght';
				$hasProblem = true;
			}
			if ((int)$aco['lft'] <= $analysisRangeStart || (int)$aco['rght'] >= $analysisRangeEnd) {
				$problems[] = $aco['alias'] . ': fuera de rango';
				$hasProblem = true;
			}

			if ((int)$aco['lft'] !== $newLft || (int)$aco['rght'] !== $newRght || $hasProblem) {
				$corrections[] = array(
					'id' => (int)$aco['id'],
					'alias' => $aco['alias'],
					'old_lft' => (int)$aco['lft'],
					'old_rght' => (int)$aco['rght'],
					'new_lft' => $newLft,
					'new_rght' => $newRght
				);
			}
		}

		$parentRghtNeedsCorrection = ((int)$parentNode['rght'] !== $expectedParentRght);
		if ($parentRghtNeedsCorrection) {
			$problems[] = $targetController . ': rght del padre incorrecto';
		}

		if ($this->request->is('post') && !empty($this->request->data['Pages']['execute_fix'])) {
			$db->begin();
			try {
				foreach ($corrections as $correction) {
					$db->query(
						'UPDATE acos SET lft = ?, rght = ? WHERE id = ?',
						array($correction['new_lft'], $correction['new_rght'], $correction['id'])
					);
				}

				$db->query(
					'UPDATE acos SET rght = ? WHERE id = ?',
					array($expectedParentRght, $parentId)
				);

				$db->commit();
				$this->Session->setFlash(__('Arbol ACL reparado exitosamente.'), 'default', array('class' => 'success'));
				return $this->redirect(array('controller' => 'pages', 'action' => 'repararArbolAcl'));
			} catch (Exception $e) {
				$db->rollback();
				$this->Session->setFlash(__('Error al reparar el arbol ACL: %s', $e->getMessage()), 'default', array('class' => 'error-message'));
			}
		}

		$rawConsistency = $db->fetchAll(
			'SELECT
				parent.alias as parent_alias,
				parent.lft as parent_lft,
				parent.rght as parent_rght,
				COUNT(child.id) as child_count,
				MIN(child.lft) as min_child_lft,
				MAX(child.rght) as max_child_rght
			FROM acos parent
			LEFT JOIN acos child ON child.parent_id = parent.id
			WHERE parent.id = ?
			GROUP BY parent.id, parent.alias, parent.lft, parent.rght',
			array($parentId)
		);
		$consistency = array();
		if (!empty($rawConsistency)) {
			$row = $rawConsistency[0];
			$parentData = !empty($row['parent']) ? $row['parent'] : array();
			$aggregateData = !empty($row[0]) ? $row[0] : array();

			$consistency[] = array(array(
				'parent_alias' => !empty($aggregateData['parent_alias']) ? $aggregateData['parent_alias'] : (!empty($parentData['parent_alias']) ? $parentData['parent_alias'] : (!empty($parentData['alias']) ? $parentData['alias'] : $targetController)),
				'parent_lft' => isset($aggregateData['parent_lft']) ? $aggregateData['parent_lft'] : (isset($parentData['parent_lft']) ? $parentData['parent_lft'] : (isset($parentData['lft']) ? $parentData['lft'] : $parentNode['lft'])),
				'parent_rght' => isset($aggregateData['parent_rght']) ? $aggregateData['parent_rght'] : (isset($parentData['parent_rght']) ? $parentData['parent_rght'] : (isset($parentData['rght']) ? $parentData['rght'] : $parentNode['rght'])),
				'child_count' => isset($aggregateData['child_count']) ? $aggregateData['child_count'] : count($children),
				'min_child_lft' => isset($aggregateData['min_child_lft']) ? $aggregateData['min_child_lft'] : null,
				'max_child_rght' => isset($aggregateData['max_child_rght']) ? $aggregateData['max_child_rght'] : null
			));
		}

		$this->set(compact(
			'targetController',
			'parentNode',
			'parentId',
			'analysisRangeStart',
			'analysisRangeEnd',
			'expectedParentRght',
			'parentRghtNeedsCorrection',
			'duplicates',
			'children',
			'problems',
			'corrections',
			'consistency'
		));
	}
}
