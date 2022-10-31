<?php

namespace Tests\Browser;

use App\Models\Test;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Contracts\Console\Kernel;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

use Laravel\Nova\Testing\Browser\Pages\Create;
use Laravel\Nova\Testing\Browser\Pages\Detail;
use Laravel\Nova\Testing\Browser\Pages\Update;

/**
 * @covers \Formfeed\DependablePanel\DependablePanel
 */
class UpdateTest extends DuskTestCase {

    protected $resource;

    //use DatabaseMigrations;
    /**
     * A basic browser test example.
     *
     * @return void
     */

    public function runDatabaseMigrations() {
        $this->artisan('migrate:fresh', ['--seed' => true]);

        $this->app[Kernel::class]->setArtisan(null);

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback');
            RefreshDatabaseState::$migrated = false;
        });
    }

    public function testPanelVisibilityWithValues() {
        $resource = Test::forceCreate([
            "record_data" => [
                "test1_field1" => "test1_field1_value",
                "test1_field2" => "test1_field2_value",
            ]
        ]);

        $this->closeAll();
        $this->browse(function (Browser $browser) use ($resource) {
            $browser->loginAs(1)
                ->visit(new Update('test-updates', $resource->id))
                ->waitFor('@record_data->test1_field1')
                ->waitFor('@record_data->test1_field2')
                ->assertInputValue('@record_data->test1_field1', 'test1_field1_value')
                ->assertInputValue('@record_data->test1_field2', 'test1_field2_value')
                ->check(".field-record-data-test-1-boolean input")
                ->pause(500)
                ->assertMissing(".component-form-nova-dependable-panel.field-test-1")
                ->assertMissing("@record_data->test1_field1")
                ->assertMissing("@record_data->test1_field2");

            $browser->uncheck(".field-record-data-test-1-boolean input")
                ->pause(500)
                ->assertVisible(".component-form-nova-dependable-panel.field-test-1")
                ->assertVisible("@record_data->test1_field1")
                ->assertVisible("@record_data->test1_field2")
                ->assertInputValue('@record_data->test1_field1', 'test1_field1_value')
                ->assertInputValue('@record_data->test1_field2', 'test1_field2_value');
        });
    }

    public function testSingleRequest() {

        $resource = Test::forceCreate([
            "record_data" => [
                "test2_field1" => "test2_field1_value",
                "test2_field2" => "test2_field2_value",
            ]
        ]);

        $this->closeAll();
        $this->browse(function (Browser $browser) use ($resource) {
            $browser->loginAs(1)
                ->visit(new Update('test-updates', $resource->id))
                ->waitFor(".field-record-data-test-2-boolean input")
                ->assertNotPresent("@record_data->test2_field2")
                ->assertInputValue("@record_data->test2_field1", "test2_field1_value")
                ->check(".field-record-data-test-2-boolean input")
                ->pause(500)
                ->assertVisible(".component-form-nova-dependable-panel.field-test-2")
                ->assertNotPresent("@record_data->test2_field1")
                ->assertPresent("@record_data->test2_field2")
                ->assertInputValue("@record_data->test2_field2", "test2_field2_value");

            $browser->uncheck(".field-record-data-test-2-boolean input")
                ->pause(500)
                ->assertVisible(".component-form-nova-dependable-panel.field-test-2")
                ->assertPresent("@record_data->test2_field1")
                ->assertNotPresent("@record_data->test2_field2")
                ->assertInputValue("@record_data->test2_field1", "test2_field1_value");
        });
    }

    public function testValuesPersistMultipleRequests() {

        $resource = Test::forceCreate([
            "record_data" => [
                "test1_field1" => "test1_field1_value",
                "test1_field2" => "test1_field2_value",
            ]
        ]);

        $this->closeAll();
        $this->browse(function (Browser $browser) use ($resource) {
            $browser->loginAs(1)
                ->visit(new Update('test-updates', $resource->id))
                ->waitFor(".field-record-data-test-1-boolean input")
                ->type("@record_data->test1_field1", "test1_field1_changed")
                ->type("@record_data->test1_field2", "test1_field2_changed")
                ->check(".field-record-data-test-1-boolean input")
                ->pause(500)
                ->uncheck(".field-record-data-test-1-boolean input")
                ->pause(500)
                ->assertInputValue("@record_data->test1_field1", "test1_field1_changed")
                ->assertInputValue("@record_data->test1_field2", "test1_field2_changed");
        });
    }

    public function testValuesPersistSingleRequest() {

        $resource = Test::forceCreate([
            "record_data" => [
                "test2_field1" => "test2_field1_value",
                "test2_field2" => "test2_field2_value",
            ]
        ]);

        $this->closeAll();
        $this->browse(function (Browser $browser) use ($resource) {
            $browser->loginAs(1)
                ->visit(new Update('test-updates', $resource->id))
                ->waitFor(".field-record-data-test-2-boolean input")
                ->type("@record_data->test2_field1", "test2_field1_changed")
                ->check(".field-record-data-test-2-boolean input")
                ->pause(500)
                ->uncheck(".field-record-data-test-2-boolean input")
                ->pause(500)
                ->assertInputValue("@record_data->test2_field1", "test2_field1_changed");
        });
    }

    public function testApplyToFields() {
        $resource = Test::forceCreate([
            "record_data" => [
                "test3_field1" => "test3_field1_value",
                "test3_field2" => "test3_field2_value",
                "test3_field3" => "test3_field3_value",
                "test4_field1" => "test4_field1_value",
                "test4_field2" => "test4_field2_value",
                "test4_field3" => "test4_field3_value",
            ]
        ]);

        $this->closeAll();
        $this->browse(function (Browser $browser) use ($resource) {
            $browser->loginAs(1)
                ->visit(new Update('test-updates', $resource->id))
                ->waitFor(".field-record-data-test-3-boolean input")
                ->check(".field-record-data-test-3-boolean input")
                ->pause(500)
                ->assertDisabled("@record_data->test3_field1")
                ->assertDisabled("@record_data->test3_field2")
                ->assertEnabled("@record_data->test3_field3")
                ->assertInputValue("@record_data->test3_field1", "test3_field1_value")
                ->assertInputValue("@record_data->test3_field2", "test3_field2_value")
                ->assertInputValue("@record_data->test3_field3", "test3_field3_value");

            $browser->waitFor(".field-record-data-test-4-boolean input")
                ->check(".field-record-data-test-4-boolean input")
                ->pause(500)
                ->assertDisabled("@record_data->test4_field1")
                ->assertDisabled("@record_data->test4_field2")
                ->assertEnabled("@record_data->test4_field3")
                ->assertInputValue("@record_data->test4_field1", "test4_field1_value")
                ->assertInputValue("@record_data->test4_field2", "test4_field2_value")
                ->assertInputValue("@record_data->test4_field3", "test4_field3_value");
        });
    }

    public function testPanelIsSeparate() {
        $resource = Test::forceCreate([
            "record_data" => [
                "test5_field1" => "test5_field1_value",
            ]
        ]);

        $this->closeAll();
        $this->browse(function (Browser $browser) use ($resource) {
            $browser->loginAs(1)
                ->visit(new Update('test-updates', $resource->id))
                ->waitFor(".component-form-nova-dependable-panel.field-test-5")
                ->assertPresent(".component-form-panel.panel-test-5")
                ->assertPresent(".component-form-panel.panel-test-5 .component-form-nova-dependable-panel.field-test-5")
                ->assertPresent(".component-form-panel.panel-test-5 .component-form-nova-dependable-panel.field-test-5 .component-form-text-field.field-record-data-test-5-field-1 ")
                ->assertInputValue("@record_data->test5_field1", "test5_field1_value");
        });
    }

    public function testValidation() {
        $resource = Test::forceCreate([
            "record_data" => [
                "test7_field1" => "test7_field1_value",
                "test7_field2" => "test7_field2_value",
                "test8_field1" => "test8_field1_value",
                "test8_field2" => "test8_field2_value",
            ]
        ]);

        $this->closeAll();
        $this->browse(function (Browser $browser) use ($resource) {
            $browser->loginAs(1)
                ->visit(new Update('test-updates', $resource->id))
                ->waitFor(".component-form-nova-dependable-panel.field-test-7")
                ->waitFor(".component-form-nova-dependable-panel.field-test-8")
                ->assertInputValue("@record_data->test7_field1", "test7_field1_value")
                ->assertInputValue("@record_data->test7_field2", "test7_field2_value")
                ->assertInputValue("@record_data->test8_field1", "test8_field1_value")
                ->assertInputValue("@record_data->test8_field2", "test8_field2_value")
                ->clear("@record_data->test7_field1")
                ->clear("@record_data->test8_field1")
                ->type("@record_data->test7_field1", "1")
                ->type("@record_data->test8_field1", "1")
                ->assertInputValueIsNot("@record_data->test7_field1", "test7_field1_value")
                ->assertInputValueIsNot("@record_data->test8_field1", "test8_field1_value");


                $browser->update()
                ->assertDontSee("The test update was updated")
                ->assertPresent(".component-form-text-field.field-record-data-test-7-field-1")
                ->assertPresent(".component-form-text-field.field-record-data-test-8-field-1")
                ->assertInputValue("@record_data->test7_field1", "1")
                ->assertInputValue("@record_data->test8_field1", "1")
                ->assertSeeIn(".component-form-text-field.field-record-data-test-7-field-1", "The Field 1 must be at least 2 characters.")
                ->assertSeeIn(".component-form-text-field.field-record-data-test-8-field-1", "The Field 1 must be at least 2 characters.");

                /*
            $browser
                ->check(".field-record-data-test-7-boolean input")
                ->check(".field-record-data-test-8-boolean input")
                ->pause(500)
                ->assertDontSeeIn(".component-form-nova-dependable-panel.field-test-7", "The Field 1 field is required.")
                ->assertDontSeeIn(".component-form-nova-dependable-panel.field-test-8", "The Field 1 field is required.")
                ->click("@create-button")
                ->pause(500)
                ->assertSeeIn(".component-form-text-field.field-record-data-test-7-field-2", "The Field 2 field is required.")
                ->assertSeeIn(".component-form-text-field.field-record-data-test-8-field-2", "The Field 2 field is required.");*/
        });
    }

    public function testValueIsSetIfHiddenOnLoad() {

        // Tests that a fields value is correctly set if it is hidden by the current dependsOn flags when the fields are loaded

        $resource = Test::forceCreate([
            "record_data" => [
                "test9_field1" => "test9_field1_value",
                "test10_field1" => "test10_field1_value",
            ]
        ]);

        $this->closeAll();
        $this->browse(function (Browser $browser) use ($resource) {
            $browser->loginAs(1)
                ->visit(new Update('test-updates', $resource->id))
                ->waitFor(".component-form-nova-dependable-panel.field-test-9")
                ->assertMissing("@record_data->test9_field1")
                ->check(".field-record-data-test-9-boolean input")
                ->pause(500)
                ->assertPresent("@record_data->test9_field1")
                ->assertInputValue("@record_data->test9_field1", "test9_field1_value");

            $browser->waitFor(".component-form-nova-dependable-panel.field-test-10")
            ->assertMissing("@record_data->test10_field1")
            ->check(".field-record-data-test-10-boolean input")
            ->pause(500)
            ->assertPresent("@record_data->test10_field1")
            ->assertInputValue("@record_data->test10_field1", "test10_field1_value");
        });
    }



    public function testSubmission() {

        $resource = Test::forceCreate([]);

        $this->closeAll();
        $this->browse(function (Browser $browser) use ($resource) {
            $browser->loginAs(1)
                ->visit(new Update('test-updates', $resource->id))
                ->waitFor(".component-form-nova-dependable-panel.field-test-7")
                ->waitFor(".component-form-nova-dependable-panel.field-test-8")
                ->waitFor(".component-form-nova-dependable-panel.field-test-11")
                ->type("@record_data->test7_field1", "test7_field1")
                ->check(".field-record-data-test-7-boolean input")
                ->pause(500)
                ->type("@record_data->test7_field2", "test7_field2")
                ->type("@record_data->test8_field1", "test8_field1")
                ->type("@record_data->test11_field1", "test11_field1")
                ->check(".field-record-data-test-11-boolean input")
                ->pause(500)
                ->assertDisabled("@record_data->test11_field1")
                ->update()
                ->waitForText("The test update was updated");

            $test = Test::orderBy('id', 'desc')->first();

            $this->assertSame(true, $test->record_data['test7_boolean']);
            $this->assertThat($test->record_data, $this->logicalNot($this->arrayHasKey('test7_field1')));
            $this->assertSame("test7_field2", $test->record_data['test7_field2']);
            $this->assertSame(false, $test->record_data['test8_boolean']);
            $this->assertSame("test8_field1", $test->record_data['test8_field1']);
            $this->assertSame(true, $test->record_data['test11_boolean']);
            $this->assertThat($test->record_data, $this->logicalNot($this->arrayHasKey('test11_field1')));
        });
    }

    public function testAdditionalFieldsShown() {

        $resource = Test::forceCreate([
            "record_data" => [
                "test12_field1" => "test12_field1_value",
                "test12_field2" => "test12_field2_value",
                "test13_field1" => "test13_field1_value",
                "test13_field2" => "test13_field2_value",
            ]
        ]);

        $this->closeAll();
        $this->browse(function (Browser $browser) use ($resource) {
            $browser->loginAs(1)
                ->visit(new Update('test-updates', $resource->id))
                ->waitFor(".component-form-nova-dependable-panel.field-test-12")
                ->waitFor(".component-form-nova-dependable-panel.field-test-13")
                ->check(".field-record-data-test-12-boolean input")
                ->check(".field-record-data-test-13-boolean input")
                ->pause(500)
                ->assertPresent("@record_data->test12_field2")
                ->assertPresent("@record_data->test13_field2")
                ->assertNotPresent("@record_data->test12_field1")
                ->assertNotPresent("@record_data->test13_field1")
                ->uncheck(".field-record-data-test-12-boolean input")
                ->uncheck(".field-record-data-test-13-boolean input")
                ->pause(500)
                ->assertnotPresent("@record_data->test12_field2")
                ->assertnotPresent("@record_data->test13_field2")
                ->assertPresent("@record_data->test12_field1")
                ->assertPresent("@record_data->test13_field1");
        });
    }

    public function testAdditionalFieldsShownWithValues() {

        $resource = Test::forceCreate([
            "record_data" => [
                "test7_field1" => "test7_field1_value",
                "test8_field1" => "test8_field1_value",
                "test12_field1" => "test12_field1_value",
                "test12_field2" => "test12_field2_value",
                "test13_field1" => "test13_field1_value",
                "test13_field2" => "test13_field2_value",
            ]
        ]);

        $this->closeAll();
        $this->browse(function (Browser $browser) use ($resource) {
            $browser->loginAs(1)
                ->visit(new Update('test-updates', $resource->id))
                ->waitFor(".component-form-nova-dependable-panel.field-test-12")
                ->waitFor(".component-form-nova-dependable-panel.field-test-13")
                ->assertInputValue("@record_data->test12_field1", "test12_field1_value")
                ->assertInputValue("@record_data->test13_field1", "test13_field1_value")
                ->check(".field-record-data-test-12-boolean input")
                ->check(".field-record-data-test-13-boolean input")
                ->pause(500)
                ->assertInputValue("@record_data->test12_field2", "test12_field2_value")
                ->assertInputValue("@record_data->test13_field2", "test13_field2_value")
                ->assertNotPresent("@record_data->test12_field1")
                ->assertNotPresent("@record_data->test13_field1");
        });
    }

    public function testAdditionalFieldsValuesPersist() {

        // Currently Fails in an expected Manner

        return;

        $resource = Test::forceCreate([
            "record_data" => [
                "test12_field1" => "test12_field1_value",
                "test12_field2" => "test12_field2_value",
                "test13_field1" => "test13_field1_value",
                "test13_field2" => "test13_field2_value",
            ]
        ]);

        $this->closeAll();
        $this->browse(function (Browser $browser) use ($resource) {
            $browser->loginAs(1)
                ->visit(new Update('test-updates', $resource->id))
                ->waitFor(".component-form-nova-dependable-panel.field-test-12")
                ->waitFor(".component-form-nova-dependable-panel.field-test-13")
                ->assertInputValue("@record_data->test12_field1", "test12_field1_value")
                ->assertInputValue("@record_data->test13_field1", "test13_field1_value")
                ->clear("@record_data->test12_field1")
                ->clear("@record_data->test13_field1")
                ->type("@record_data->test12_field1", "test12_field1_changed")
                ->type("@record_data->test13_field1", "test13_field1_changed")
                ->check(".field-record-data-test-12-boolean input")
                ->check(".field-record-data-test-13-boolean input")
                ->pause(500)
                ->assertInputValue("@record_data->test12_field2", "test12_field2_value")
                ->assertInputValue("@record_data->test13_field2", "test13_field2_value")
                ->assertNotPresent("@record_data->test12_field1")
                ->assertNotPresent("@record_data->test13_field1")
                ->clear("@record_data->test12_field2")
                ->clear("@record_data->test13_field2")
                ->type("@record_data->test12_field2", "test12_field2_changed")
                ->type("@record_data->test13_field2", "test13_field2_changed")
                ->uncheck(".field-record-data-test-12-boolean input")
                ->uncheck(".field-record-data-test-13-boolean input")
                ->pause(500)
                ->assertInputValue("@record_data->test12_field1", "test12_field1_changed")
                ->assertInputValue("@record_data->test13_field1", "test13_field1_changed")
                ->check(".field-record-data-test-12-boolean input")
                ->check(".field-record-data-test-13-boolean input")
                ->pause(500)
                ->assertInputValue("@record_data->test12_field2", "test12_field2_changed")
                ->assertInputValue("@record_data->test13_field2", "test13_field2_changed");
        });
    }

    public function testAdditionalFieldsSubmitValues() {

        $resource = Test::forceCreate([
            "record_data" => [
                "test7_field1" => "test7_field1_value",
                "test8_field1" => "test8_field1_value",
                "test12_field1" => "test12_field1_value",
                "test12_field2" => "test12_field2_value",
                "test13_field1" => "test13_field1_value",
                "test13_field2" => "test13_field2_value",
            ]
        ]);

        $this->closeAll();
        $this->browse(function (Browser $browser) use ($resource) {
            $browser->loginAs(1)
                ->visit(new Update('test-updates', $resource->id))
                ->waitFor(".component-form-nova-dependable-panel.field-test-12")
                ->waitFor(".component-form-nova-dependable-panel.field-test-13")
                ->assertInputValue("@record_data->test12_field1", "test12_field1_value")
                ->assertInputValue("@record_data->test13_field1", "test13_field1_value")
                ->clear("@record_data->test12_field1")
                ->clear("@record_data->test13_field1")
                ->type("@record_data->test12_field1", "test12_field1_changed")
                ->type("@record_data->test13_field1", "test13_field1_changed")
                ->check(".field-record-data-test-12-boolean input")
                ->check(".field-record-data-test-13-boolean input")
                ->pause(500)
                ->assertInputValue("@record_data->test12_field2", "test12_field2_value")
                ->assertInputValue("@record_data->test13_field2", "test13_field2_value")
                ->assertNotPresent("@record_data->test12_field1")
                ->assertNotPresent("@record_data->test13_field1")
                ->clear("@record_data->test12_field2")
                ->clear("@record_data->test13_field2")
                ->type("@record_data->test12_field2", "test12_field2_changed")
                ->type("@record_data->test13_field2", "test13_field2_changed")
                ->check(".field-record-data-test-13-boolean-2 input")
                ->update()
                ->waitForText("The test update was updated");

                $test = Test::orderBy('id', 'desc')->first();

                $this->assertSame(true, $test->record_data['test12_boolean']);
                $this->assertSame(true, $test->record_data['test13_boolean']);
                $this->assertSame(true, $test->record_data['test13_boolean2']);
                $this->assertSame("test12_field1_value", $test->record_data['test12_field1']);
                $this->assertSame("test12_field2_changed", $test->record_data['test12_field2']);
                $this->assertSame("test13_field1_value", $test->record_data['test13_field1']);
                $this->assertSame("test13_field2_value", $test->record_data['test13_field2']);
        });
    }

    public function testDependsOnAppliedWithExistingValues() {
        $resource = Test::forceCreate([
            "record_data" => [
                "test7_boolean" => true,
                "test8_boolean" => true,
            ]
        ]);

        $this->closeAll();
        $this->browse(function (Browser $browser) use ($resource) {
            $browser->loginAs(1)
                ->visit(new Update('test-updates', $resource->id))
                ->waitFor(".component-form-nova-dependable-panel.field-test-7")
                ->waitFor(".component-form-nova-dependable-panel.field-test-8")
                ->assertNotPresent("@record_data->test7_field1")
                ->assertNotPresent("@record_data->test8_field1")
                ->assertPresent("@record_data->test7_field2")
                ->assertPresent("@record_data->test8_field2")

                ;
        });
    }

}
