<?php
namespace Duplicatable\Test\Fixture;

use Cake\ORM\Table;
use Cake\TestSuite\Fixture\TestFixture;

class InvoiceItemsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        // associations
        $this->hasMany('InvoiceItemProperties', ['className' => 'Duplicatable\Test\Fixture\InvoiceItemPropertiesTable']);
        $this->hasMany('InvoiceItemVariations', ['className' => 'Duplicatable\Test\Fixture\InvoiceItemVariationsTable']);
    }
}

class InvoiceItemsFixture extends TestFixture
{
    public $fields = [
        'id' => ['type' => 'integer'],
        'invoice_id' => ['type' => 'integer', 'default' => null, 'null' => true],
        'name' => ['type' => 'string', 'default' => null, 'null' => true],
        'amount' => ['type' => 'float', 'default' => null, 'null' => true],
        'created' => ['type' => 'datetime', 'default' => null, 'null' => true],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']]
        ]
    ];

    public $records = [
        [
            'id'         => 1,
            'invoice_id' => 1,
            'name'       => 'Item 1',
            'amount'     => 10.2,
            'created'    => '2015-03-17 01:20:48'
        ],
        [
            'id'         => 2,
            'invoice_id' => 1,
            'name'       => 'Item 2',
            'amount'     => 5.3,
            'created'    => '2015-03-17 01:21:20'
        ],
        [
            'id'         => 3,
            'invoice_id' => 2,
            'name'       => 'Item',
            'amount'     => 150,
            'created'    => '2015-05-17 03:49:20'
        ]
    ];
}
