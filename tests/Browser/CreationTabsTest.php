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


/**
 * @covers \Formfeed\DependablePanel\DependablePanel
 */
class CreationTabsTest extends DuskTestCase {

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

    public function testFieldVisbility() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('test-creation-tabs'))
                ->waitFor(".field-record-data-test-1-boolean input")
                ->type("@record_data->test1_field1", "test1_field1")
                ->check(".field-record-data-test-1-boolean input")
                ->pause(500)
                ->assertMissing(".component-form-nova-dependable-panel.field-test-1-panel")
                ->assertNotPresent("@record_data->test1_field1");

            $browser->uncheck(".field-record-data-test-1-boolean input")
                ->pause(500)
                ->assertVisible(".component-form-nova-dependable-panel.field-test-1-panel")
                ->assertPresent("@record_data->test1_field1")
                ->assertInputValue("@record_data->test1_field1", "test1_field1");

            $browser
                ->waitFor(".field-record-data-test-2-boolean input")
                ->type("@record_data->test2_field1", "test2_field1")
                ->check(".field-record-data-test-2-boolean input")
                ->pause(500)
                ->assertMissing(".component-form-nova-dependable-panel.field-test-2-panel")
                ->assertNotPresent("@record_data->test2_field1");

            $browser->uncheck(".field-record-data-test-2-boolean input")
                ->pause(500)
                ->assertVisible(".component-form-nova-dependable-panel.field-test-2-panel")
                ->assertPresent("@record_data->test2_field1")
                ->assertInputValue("@record_data->test2_field1", "test2_field1");
        });
    }

    public function testPanelVisbility() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('test-creation-tabs'))
                ->waitFor(".field-record-data-test-3-boolean input")
                ->check(".field-record-data-test-3-boolean input")
                ->pause(500)
                ->assertMissing(".component-form-nova-dependable-panel.field-test-3-panel")
                ->assertMissing("@record_data->test3_field1");

            $browser->uncheck(".field-record-data-test-3-boolean input")
                ->pause(500)
                ->assertVisible(".component-form-nova-dependable-panel.field-test-3-panel")
                ->assertVisible("@record_data->test3_field1");
        });
    }

    public function testTabValidationFlag() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('test-creation-tabs'))
                ->waitFor(".field-record-data-test-4-boolean input")
                ->assertInputValue("@record_data->test4_field1", "")
                ->create()
                ->pause(500)
                ->assertAttributeContains("@test-tab-1-tab", "class", "tab-has-error")
                ->type("@record_data->test4_field1", "test4_field1")
                ->create()
                ->waitForText("The test creation tab was created");

        });

        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('test-creation-tabs'))
                ->waitFor(".field-record-data-test-5-boolean input")
                ->assertInputValue("@record_data->test5_field1", "")
                ->check(".field-record-data-test-5-boolean input")
                ->type("@record_data->test4_field1", "test4_field1")
                ->create()
                ->pause(500)
                ->assertSeeIn(".component-form-text-field.field-record-data-test-5-field-1", "The Field 1 field is required.")
                ->assertDontSeeIn(".component-form-text-field.field-record-data-test-4-field-1", "The Field 1 field is required.")
                ->assertAttributeContains("@test-tab-1-tab", "class", "tab-has-error")
                ->type("@record_data->test5_field1", "test5_field1")
                ->create()
                ->waitForText("The test creation tab was created");
        });
    }

    public function testNoSeparatePanelOnTabs() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('test-creation-tabs'))
                ->waitFor(".component-form-nova-dependable-panel.field-test-6-panel")
                ->assertPresent(".component-form-nova-dependable-panel.field-test-6-panel")
                ->assertNotPresent(".component-form-panel.panel-test-6-panel")
                ->assertNotPresent(".component-form-panel.panel-test-6-panel .component-form-nova-dependable-panel.field-test-6-panel");
        });
    }

    public function testDependsOnWorksAcrossTabs() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('test-creation-tabs'))
                ->waitFor(".field-record-data-test-7-boolean input")
                ->click("@test-tab-2-tab")
                ->pause(100)
                ->assertPresent("@record_data->test7_field1")
                ->assertVisible("@record_data->test7_field1")
                ->assertNotPresent("@record_data->test7_field2")
                ->assertMissing("@record_data->test7_field2")
                ->click("@test-tab-1-tab")
                ->check(".field-record-data-test-7-boolean input")
                ->pause(500)
                ->click("@test-tab-2-tab")
                ->pause(100)
                ->assertPresent("@record_data->test7_field2")
                ->assertVisible("@record_data->test7_field2")
                ->assertNotPresent("@record_data->test7_field1")
                ->assertMissing("@record_data->test7_field1")
                ->create()
                ->pause(500)
                ->assertAttributeContains("@test-tab-1-tab", "class", "tab-has-error")
                ->assertAttributeContains("@test-tab-2-tab", "class", "tab-has-error");
        });
    }
}
