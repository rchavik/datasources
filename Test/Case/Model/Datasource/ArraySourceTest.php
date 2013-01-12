<?php
/**
 * Array Datasource Test file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       datasources
 * @subpackage    datasources.tests.cases.models.datasources
 * @since         CakePHP Datasources v 0.3
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('ArraySource', 'Datasources.Model/Datasource');
App::uses('ConnectionManager', 'Model');

// Add new db config
ConnectionManager::create('test_array', array('datasource' => 'Datasources.ArraySource'));

class EmptyModel extends CakeTestModel {

/**
 * Database Configuration
 *
 * @var string
 * @access public
 */
	public $useDbConfig = 'test_array';

/**
 * Set recursive
 *
 * @var integer
 * @access public
 */
	public $recursive = -1;

/**
 * Records
 *
 * @var array
 * @access public
 */
	public $records = array();

}

/**
 * Array Testing Model
 *
 */
class ArrayModel extends CakeTestModel {

/**
 * Database Configuration
 *
 * @var string
 * @access public
 */
	public $useDbConfig = 'test_array';

/**
 * Set recursive
 *
 * @var integer
 * @access public
 */
	public $recursive = -1;

/**
 * Records
 *
 * @var array
 * @access public
 */
	public $records = array(
		array(
			'id' => 1,
			'name' => 'USA',
			'relate_id' => 1
		),
		array(
			'id' => 2,
			'name' => 'Brazil',
			'relate_id' => 1
		),
		array(
			'id' => 3,
			'name' => 'Germany',
			'relate_id' => 2
		)
	);
}

/**
 * ArraysRelate Testing Model
 *
 */
class ArraysRelateModel extends CakeTestModel {

/**
 * Database Configuration
 *
 * @var string
 * @access public
 */
	public $useDbConfig = 'test_array';

/**
 * Records
 *
 * @var array
 * @access public
 */
	public $records = array(
		array('array_model_id' => 1, 'relate_id' => 1),
		array('array_model_id' => 1, 'relate_id' => 2),
		array('array_model_id' => 1, 'relate_id' => 3),
		array('array_model_id' => 2, 'relate_id' => 1),
		array('array_model_id' => 2, 'relate_id' => 3),
		array('array_model_id' => 3, 'relate_id' => 1),
		array('array_model_id' => 3, 'relate_id' => 2)
	);
}

/**
 * User Testing Model
 *
 */
class UserModel extends CakeTestModel {

/**
 * Use DB Config
 *
 * @var string
 * @access public
 */
	public $useDbConfig = 'test';

/**
 * Use Table
 *
 * @var string
 * @access public
 */
	public $useTable = 'users';

/**
 * Belongs To
 *
 * @var array
 * @access public
 */
	public $belongsTo = array(
		'Born' => array(
			'className' => 'ArrayModel',
			'foreignKey' => 'born_id',
		)
	);
}

/**
 * Array Datasource Test
 *
 */
class ArraySourceTest extends CakeTestCase {

/**
 * Array Source Instance
 *
 * @var ArraySource
 * @access public
 */
	public $Model = null;

/**
 * Set up for Tests
 *
 * @return void
 * @access public
 */
	public function setUp() {
		parent::setUp();
		$this->Model = ClassRegistry::init('ArrayModel');
	}

	public function testEmptyModel() {
		$Model = ClassRegistry::init('EmptyModel');
		$result = $Model->find('all');
		$this->assertSame(array(), $result);
	}

/**
 * testFindAll
 *
 * @return void
 * @access public
 */
	public function testFindAll() {
		$result = $this->Model->find('all');
		$expected = array(
			array('ArrayModel' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1)),
			array('ArrayModel' => array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1)),
			array('ArrayModel' => array('id' => 3, 'name' => 'Germany', 'relate_id' => 2))
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testFindFields
 *
 * @return void
 * @access public
 */
	public function testFindFields() {
		$expected = array(
			array('ArrayModel' => array('id' => 1)),
			array('ArrayModel' => array('id' => 2)),
			array('ArrayModel' => array('id' => 3))
		);
		$result = $this->Model->find('all', array('fields' => array('id')));
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('all', array('fields' => array('ArrayModel.id')));
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('all', array('fields' => array('ArrayModel.id', 'Unknow.id')));
		$this->assertEquals($expected, $result);
	}


/**
 * testField
 *
 * @return void
 * @access public
 */
	public function testField() {
		$expected = 2;
		$result = $this->Model->field('id', array('name' => 'Brazil'));
		$this->assertEquals($expected, $result);

		$expected = 'Germany';
		$result = $this->Model->field('name', array('relate_id' => 2));
		$this->assertEquals($expected, $result);

		$expected = 'USA';
		$result = $this->Model->field('name', array('relate_id' => 1));
		$this->assertEquals($expected, $result);
	}


/**
 * testFindLimit
 *
 * @return void
 * @access public
 */
	public function testFindLimit() {
		$result = $this->Model->find('all', array('limit' => 2));
		$expected = array(
			array('ArrayModel' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1)),
			array('ArrayModel' => array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1))
		);
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('all', array('limit' => 2, 'page' => 2));
		$expected = array(
			array('ArrayModel' => array('id' => 3, 'name' => 'Germany', 'relate_id' => 2))
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testFindOrder
 *
 * @return void
 * @access public
 */
	public function testFindOrder() {
		$result = $this->Model->find('all', array('order' => 'ArrayModel.name'));
		$expected = array(
			array('ArrayModel' => array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1)),
			array('ArrayModel' => array('id' => 3, 'name' => 'Germany', 'relate_id' => 2)),
			array('ArrayModel' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1))
		);
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('all', array('fields' => array('ArrayModel.id'), 'order' => 'ArrayModel.name'));
		$expected = array(
			array('ArrayModel' => array('id' => 2)),
			array('ArrayModel' => array('id' => 3)),
			array('ArrayModel' => array('id' => 1)),
		);
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('all', array('fields' => array('ArrayModel.id'), 'order' => 'ArrayModel.name', 'limit' => 1, 'page' => 2));
		$expected = array(
			array('ArrayModel' => array('id' => 3))
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testFindConditions
 *
 * @return void
 * @access public
 */
	public function testFindConditions() {
		$result = $this->Model->find('all', array('conditions' => array('ArrayModel.name' => 'USA')));
		$expected = array(array('ArrayModel' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1)));
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('all', array('conditions' => array('ArrayModel.name =' => 'USA')));
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('all', array('conditions' => array('ArrayModel.name = USA')));
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('all', array('conditions' => array('ArrayModel.name !=' => 'USA')));
		$expected = array(array('ArrayModel' => array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1)), array('ArrayModel' => array('id' => 3, 'name' => 'Germany', 'relate_id' => 2)));
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('all', array('conditions' => array('ArrayModel.name != USA')));
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('all', array('conditions' => array('ArrayModel.name LIKE' => '%ra%')));
		$expected = array(array('ArrayModel' => array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1)));
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('all', array('conditions' => array('ArrayModel.name LIKE %ra%')));
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('all', array('conditions' => array('ArrayModel.name LIKE _r%')));
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('all', array('conditions' => array('ArrayModel.name LIKE %b%')));
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('all', array('conditions' => array('ArrayModel.name LIKE %a%')));
		$expected = array(array('ArrayModel' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1)), array('ArrayModel' => array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1)), array('ArrayModel' => array('id' => 3, 'name' => 'Germany', 'relate_id' => 2)));
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('all', array('conditions' => array('ArrayModel.name' => array('USA', 'Germany'))));
		$expected = array(array('ArrayModel' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1)), array('ArrayModel' => array('id' => 3, 'name' => 'Germany', 'relate_id' => 2)));
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('all', array('conditions' => array('ArrayModel.name IN (USA, Germany)')));
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('all', array('conditions' => array('ArrayModel.name' => 'USA', 'ArrayModel.id' => 2)));
		$expected = array();
		$this->assertSame($expected, $result);
	}

/**
 * testFindconditionsRecursive
 *
 * @return void
 * @access public
 */
	public function testFindConditionsRecursive() {
		$result = $this->Model->find('all', array('conditions' => array('AND' => array('ArrayModel.name' => 'USA', 'ArrayModel.id' => 2))));
		$expected = array();
		$this->assertSame($expected, $result);

		$result = $this->Model->find('all', array('conditions' => array('OR' => array('ArrayModel.name' => 'USA', 'ArrayModel.id' => 2))));
		$expected = array(
			array('ArrayModel' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1)),
			array('ArrayModel' => array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1))
		);
		$this->assertSame($expected, $result);

		$result = $this->Model->find('all', array('conditions' => array('NOT' => array('ArrayModel.id' => 2))));
		$expected = array(
			array('ArrayModel' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1)),
			array('ArrayModel' => array('id' => 3, 'name' => 'Germany', 'relate_id' => 2))
		);
		$this->assertSame($expected, $result);
	}

/**
 * testFindFirst
 *
 * @return void
 * @access public
 */
	public function testFindFirst() {
		$result = $this->Model->find('first');
		$expected = array('ArrayModel' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1));
		$this->assertEquals($expected, $result);

		$result = $this->Model->find('first', array('fields' => array('name')));
		$expected = array('ArrayModel' => array('name' => 'USA'));
		$this->assertEquals($expected, $result);
	}

/**
 * testFindCount
 *
 * @return void
 * @access public
 */
	public function testFindCount() {
		$result = $this->Model->find('count');
		$this->assertEquals($result, 3);

		$result = $this->Model->find('count', array('limit' => 2));
		$this->assertEquals($result, 2);

		$result = $this->Model->find('count', array('limit' => 5));
		$this->assertEquals($result, 3);

		$result = $this->Model->find('count', array('limit' => 2, 'page' => 2));
		$this->assertEquals($result, 1);
	}

/**
 * testFindList
 *
 * @return void
 * @access public
 */
	public function testFindList() {
		$result = $this->Model->find('list');
		$expected = array(1 => 1, 2 => 2, 3 => 3);
		$this->assertEquals($expected, $result);

		$this->Model->displayField = 'name';
		$result = $this->Model->find('list');
		$expected = array(1 => 'USA', 2 => 'Brazil', 3 => 'Germany');
		$this->assertEquals($expected, $result);
	}

/**
 * testRead
 *
 * @return void
 * @access public
 */
	public function testRead() {
		$result = $this->Model->read(null, 1);
		$expected = array('ArrayModel' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1));
		$this->assertEquals($expected, $result);

		$result = $this->Model->read(array('name'), 2);
		$expected = array('ArrayModel' => array('name' => 'Brazil'));
		$this->assertEquals($expected, $result);
	}
}

/**
 * Interact with Dbo Test
 *
 */
class IntractModelTest extends CakeTestCase {

/**
 * List of fixtures
 *
 * @var array
 * @access public
 */
	public $fixtures = array('plugin.tools.user');

/**
 * skip
 *
 * @return void
 * @access public
 */
	public function skip() {
		$db = ConnectionManager::getDataSource('test');
		$this->skipUnless(is_subclass_of($db, 'DboSource'), '%s because database test not extends one DBO driver.');
	}

/**
 * testDboToArrayBelongsTo
 *
 * @return void
 * @access public
 */
	public function testDboToArrayBelongsTo() {
		ClassRegistry::config(array());
		$Model = ClassRegistry::init('UserModel');

		$result = $Model->find('all', array('recursive' => 0));
		$expected = array(
			array('UserModel' => array('id' => 1, 'born_id' => 1, 'name' => 'User 1'), 'Born' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1)),
			array('UserModel' => array('id' => 2, 'born_id' => 2, 'name' => 'User 2'), 'Born' => array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1)),
			array('UserModel' => array('id' => 3, 'born_id' => 1, 'name' => 'User 3'), 'Born' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1)),
			array('UserModel' => array('id' => 4, 'born_id' => 3, 'name' => 'User 4'), 'Born' => array('id' => 3, 'name' => 'Germany', 'relate_id' => 2))
		);
		$this->assertEquals($expected, $result);

		$Model->belongsTo['Born']['fields'] = array('name');
		$result = $Model->find('all', array('recursive' => 0));
		$expected = array(
			array('UserModel' => array('id' => 1, 'born_id' => 1, 'name' => 'User 1'), 'Born' => array('name' => 'USA')),
			array('UserModel' => array('id' => 2, 'born_id' => 2, 'name' => 'User 2'), 'Born' => array('name' => 'Brazil')),
			array('UserModel' => array('id' => 3, 'born_id' => 1, 'name' => 'User 3'), 'Born' => array('name' => 'USA')),
			array('UserModel' => array('id' => 4, 'born_id' => 3, 'name' => 'User 4'), 'Born' => array('name' => 'Germany'))
		);
		$this->assertEquals($expected, $result);

		$result = $Model->read(null, 1);
		$expected = array('UserModel' => array('id' => 1, 'born_id' => 1, 'name' => 'User 1'), 'Born' => array('name' => 'USA'));
		$this->assertEquals($expected, $result);
	}

/**
 * testDboToArrayBelongsToWithoutForeignKey
 *
 * @return void
 * @access public
 */
	public function testDboToArrayBelongsToWithoutForeignKey() {
		ClassRegistry::config(array());
		$Model = ClassRegistry::init('UserModel');

		$result = $Model->find('all', array(
			'fields' => array('UserModel.id', 'UserModel.name'),
			'recursive' => 0
		));
		$expected = array(
			array(
				'UserModel' => array('id' => 1, 'name' => 'User 1'),
				'Born' => array()
			),
			array(
				'UserModel' => array('id' => 2, 'name' => 'User 2'),
				'Born' => array()
			),
			array(
				'UserModel' => array('id' => 3, 'name' => 'User 3'),
				'Born' => array()
			),
			array(
				'UserModel' => array('id' => 4, 'name' => 'User 4'),
				'Born' => array()
			)
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testDboToArrayHasMany
 *
 * @return void
 * @access public
 */
	public function testDboToArrayHasMany() {
		ClassRegistry::config(array());
		$Model = ClassRegistry::init('UserModel');
		$Model->unBindModel(array('belongsTo' => array('Born')), false);
		$Model->bindModel(array('hasMany' => array('Relate' => array('className' => 'ArrayModel', 'foreignKey' => 'relate_id'))), false);

		$result = $Model->find('all', array('recursive' => 1));
		$expected = array(
			array(
				'UserModel' => array('id' => 1, 'name' => 'User 1', 'born_id' => 1),
				'Relate' => array(
					array('id' => 1, 'name' => 'USA', 'relate_id' => 1),
					array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1)
				),
			),
			array('UserModel' => array('id' => 2, 'name' => 'User 2', 'born_id' => 2),
				'Relate' => array(
					array('id' => 3, 'name' => 'Germany', 'relate_id' => 2)
				),
			),
			array('UserModel' => array('id' => 3, 'name' => 'User 3', 'born_id' => 1),
				'Relate' => array(
				),
			),
			array('UserModel' => array('id' => 4, 'name' => 'User 4', 'born_id' => 3),
				'Relate' => array(
				),
			)
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testDboToArrayHasOne
 *
 * @return void
 * @access public
 */
	public function testDboToArrayHasOne() {
		ClassRegistry::config(array());
		$Model = ClassRegistry::init('UserModel');
		$Model->unBindModel(array('hasMany' => array('Relate')), false);
		$Model->bindModel(array('hasOne' => array('Relate' => array('className' => 'ArrayModel', 'foreignKey' => 'relate_id'))), false);

		$result = $Model->find('all', array('recursive' => 1));
		$expected = array(
			array(
				'UserModel' => array('id' => 1, 'name' => 'User 1', 'born_id' => 1),
				'Relate' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1),
			),
			array('UserModel' => array('id' => 2, 'name' => 'User 2', 'born_id' => 2),
				'Relate' => array('id' => 3, 'name' => 'Germany', 'relate_id' => 2),
			),
			array(
				'UserModel' => array('id' => 3, 'name' => 'User 3', 'born_id' => 1),
				'Relate' => array()
			),
			array(
				'UserModel' => array('id' => 4, 'name' => 'User 4', 'born_id' => 3),
				'Relate' => array()
			)
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testArrayToArrayBelongsTo
 *
 * @return void
 * @access public
 */
	public function testArrayToArrayBelongsTo() {
		ClassRegistry::config(array());
		$Model = ClassRegistry::init('ArrayModel');
		$Model->recursive = 0;
		$Model->bindModel(array('belongsTo' => array('Relate' => array('className' => 'ArrayModel', 'foreignKey' => 'relate_id'))), false);

		$result = $Model->find('all');
		$expected = array(
			array(
				'ArrayModel' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1),
				'Relate' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1)
			),
			array(
				'ArrayModel' => array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1),
				'Relate' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1)
			),
			array(
				'ArrayModel' => array('id' => 3, 'name' => 'Germany', 'relate_id' => 2),
				'Relate' => array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1)
			)
		);
		$this->assertEquals($expected, $result);

		$Model->belongsTo['Relate']['fields'] = array('name');

		$result = $Model->find('all');
		$expected = array(
			array(
				'ArrayModel' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1),
				'Relate' => array('name' => 'USA')
			),
			array(
				'ArrayModel' => array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1),
				'Relate' => array('name' => 'USA')
			),
			array(
				'ArrayModel' => array('id' => 3, 'name' => 'Germany', 'relate_id' => 2),
				'Relate' => array('name' => 'Brazil')
			)
		);
		$this->assertEquals($expected, $result);

		$result = $Model->read(null, 1);
		$expected = array(
			'ArrayModel' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1),
			'Relate' => array('name' => 'USA')
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testArrayToArrayBelongsToWithoutForeignKey
 *
 * @return void
 * @access public
 */
	public function testArrayToArrayBelongsToWithoutForeignKey() {
		ClassRegistry::config(array());
		$Model = ClassRegistry::init('ArrayModel');

		$result = $Model->find('all', array(
			'fields' => array('ArrayModel.id', 'ArrayModel.name')
		));
		$expected = array(
			array(
				'ArrayModel' => array('id' => 1, 'name' => 'USA'),
				'Relate' => array()
			),
			array(
				'ArrayModel' => array('id' => 2, 'name' => 'Brazil'),
				'Relate' => array()
			),
			array(
				'ArrayModel' => array('id' => 3, 'name' => 'Germany'),
				'Relate' => array()
			)
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testArrayToArrayHasMany
 *
 * @return void
 * @access public
 */
	public function testArrayToArrayHasMany() {
		ClassRegistry::config(array());
		$Model = ClassRegistry::init('ArrayModel');
		$Model->unBindModel(array('belongsTo' => array('Relate')), false);
		$Model->bindModel(array('hasMany' => array('Relate' => array('className' => 'ArrayModel', 'foreignKey' => 'relate_id'))), false);

		$result = $Model->find('all', array('recursive' => 1));
		$expected = array(
			array(
				'ArrayModel' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1),
				'Relate' => array(
					array('id' => 1, 'name' => 'USA', 'relate_id' => 1),
					array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1)
				),
			),
			array('ArrayModel' => array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1),
				'Relate' => array(
					array('id' => 3, 'name' => 'Germany', 'relate_id' => 2)
				),
			),
			array('ArrayModel' => array('id' => 3, 'name' => 'Germany', 'relate_id' => 2),
				'Relate' => array(),
			)
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testArrayToArrayHasOne
 *
 * @return void
 * @access public
 */
	public function testArrayToArrayHasOne() {
		ClassRegistry::config(array());
		$Model = ClassRegistry::init('ArrayModel');
		$Model->unBindModel(array('hasMany' => array('Relate')), false);
		$Model->bindModel(array('hasOne' => array('Relate' => array('className' => 'ArrayModel', 'foreignKey' => 'relate_id'))), false);

		$result = $Model->find('all', array('recursive' => 1));
		$expected = array(
			array(
				'ArrayModel' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1),
				'Relate' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1)
			),
			array(
				'ArrayModel' => array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1),
				'Relate' => array('id' => 3, 'name' => 'Germany', 'relate_id' => 2)
			),
			array(
				'ArrayModel' => array('id' => 3, 'name' => 'Germany', 'relate_id' => 2),
				'Relate' => array()
			)
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testArrayToArrayHasAndBelongsToMany
 *
 * @return void
 * @access public
 */
	public function testArrayToArrayHasAndBelongsToMany() {
		ClassRegistry::config(array());
		$Model = ClassRegistry::init('ArrayModel');
		$Model->unBindModel(array('hasOne' => array('Relate')), false);
		$Model->bindModel(array('hasAndBelongsToMany' => array('Relate' => array('className' => 'ArrayModel', 'with' => 'ArraysRelateModel', 'associationForeignKey' => 'relate_id'))), false);

		$result = $Model->find('all', array('recursive' => 1));
		$expected = array(
			array(
				'ArrayModel' => array('id' => 1, 'name' => 'USA', 'relate_id' => 1),
				'Relate' => array(
					array('id' => 1, 'name' => 'USA', 'relate_id' => 1),
					array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1),
					array('id' => 3, 'name' => 'Germany', 'relate_id' => 2)
				),
			),
			array(
				'ArrayModel' => array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1),
				'Relate' => array(
					array('id' => 1, 'name' => 'USA', 'relate_id' => 1),
					array('id' => 3, 'name' => 'Germany', 'relate_id' => 2)
				),
			),
			array(
				'ArrayModel' => array('id' => 3, 'name' => 'Germany', 'relate_id' => 2),
				'Relate' => array(
					array('id' => 1, 'name' => 'USA', 'relate_id' => 1),
					array('id' => 2, 'name' => 'Brazil', 'relate_id' => 1)
				),
			)
		);
		$this->assertEquals($expected, $result);
	}
}
