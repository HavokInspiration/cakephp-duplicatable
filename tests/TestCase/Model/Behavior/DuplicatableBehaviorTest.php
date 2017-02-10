<?php
namespace Duplicatable\Test\TestCase\Model\Behavior;

use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * DuplicatableBehavior Test Case
 */
class DuplicatableBehaviorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.Duplicatable.invoice_types',
        'plugin.Duplicatable.invoices',
        'plugin.Duplicatable.invoice_items',
        'plugin.Duplicatable.invoice_item_properties',
        'plugin.Duplicatable.invoice_item_variations',
        'plugin.Duplicatable.invoices_tags',
        'plugin.Duplicatable.i18n',
        'plugin.Duplicatable.tags'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Invoices = TableRegistry::get('Invoices', [
            'className' => 'TestApp\Model\Table\InvoicesTable'
        ]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Invoices);

        parent::tearDown();
    }

    /**
     * Test duplicating with deeply nested associations
     *
     * @return void
     */
    public function testDuplicate()
    {
        $result = $this->Invoices->duplicate(1);
        $this->assertInstanceOf('Cake\Datasource\EntityInterface', $result);

        $invoice = $this->Invoices->get($result->id, [
            'contain' => [
                'InvoiceItems.InvoiceItemProperties',
                'InvoiceItems.InvoiceItemVariations',
                'Tags'
            ]
        ]);

        // entity
        $this->assertEquals('Invoice name - copy', $invoice->name);
        $this->assertEquals('Contact name', $invoice->contact_name);
        $this->assertEquals(1, $invoice->copied);
        $this->assertEquals(null, $invoice->created);

        // has many
        $this->assertEquals('Item 1', $invoice->items[0]->name);
        $this->assertEquals(null, $invoice->items[0]->created);
        $this->assertEquals('Item 2', $invoice->items[1]->name);
        $this->assertEquals(null, $invoice->items[1]->created);

        // double has many
        $this->assertEquals('NEW Property 1', $invoice->items[0]->invoice_item_properties[0]->name);
        $this->assertEquals('NEW Property 2', $invoice->items[0]->invoice_item_properties[1]->name);
        $this->assertEquals('NEW Property 3', $invoice->items[1]->invoice_item_properties[0]->name);
        $this->assertEquals('Variation 1', $invoice->items[0]->invoice_item_variations[0]->name);
        $this->assertEquals('Variation 2', $invoice->items[1]->invoice_item_variations[0]->name);
        $this->assertEquals('Variation 3', $invoice->items[1]->invoice_item_variations[1]->name);

        // belongs to
        $this->assertEquals(2, $invoice->invoice_type_id);

        // check that invoice types are not duplicated
        $this->assertEquals(2, $this->Invoices->InvoiceTypes->find()->count());

        // belongs to many
        $this->assertEquals(1, $invoice->tags[0]->id);
        $this->assertEquals('Tag 1', $invoice->tags[0]->name);
        $this->assertEquals(2, $invoice->tags[1]->id);
        $this->assertEquals('Tag 2', $invoice->tags[1]->name);

        // check that tags are not duplicated
        $this->assertEquals(2, $this->Invoices->Tags->find()->count());

        // check original entity
        $original = $this->Invoices->get(1, [
            'contain' => [
                'InvoiceItems.InvoiceItemProperties',
                'InvoiceItems.InvoiceItemVariations',
                'Tags'
            ]
        ]);

        // has many
        $this->assertEquals('Property 1', $original->items[0]->invoice_item_properties[0]->name);
        $this->assertEquals('Property 2', $original->items[0]->invoice_item_properties[1]->name);
        $this->assertEquals('Property 3', $original->items[1]->invoice_item_properties[0]->name);
        $this->assertEquals('Variation 1', $original->items[0]->invoice_item_variations[0]->name);
        $this->assertEquals('Variation 2', $original->items[1]->invoice_item_variations[0]->name);
        $this->assertEquals('Variation 3', $original->items[1]->invoice_item_variations[1]->name);

        // belongs to many
        $this->assertEquals(2, count($original->tags));
    }
}
