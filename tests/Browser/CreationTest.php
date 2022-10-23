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

class CreationTest extends DuskTestCase {

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

    public function testPanelVisibility() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('tests'))
                ->waitFor(".field-record-data-test-1-hidden input")
                ->check(".field-record-data-test-1-hidden input")
                ->pause(500)
                ->assertMissing(".component-form-nova-dependable-panel.field-test-1")
                ->assertMissing("@record_data->test1_field1")
                ->assertMissing("@record_data->test1_field2");

            $browser->uncheck(".field-record-data-test-1-hidden input")
                ->pause(500)
                ->assertVisible(".component-form-nova-dependable-panel.field-test-1")
                ->assertVisible("@record_data->test1_field1")
                ->assertVisible("@record_data->test1_field2");
        });
    }

    public function testSingleRequest() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('tests'))
                ->waitFor(".field-record-data-test-2-boolean input")
                ->assertNotPresent("@record_data->test2_field3")
                ->check(".field-record-data-test-2-boolean input")
                ->pause(500)
                ->assertVisible(".component-form-nova-dependable-panel.field-test-2")
                ->assertNotPresent("@record_data->test2_field1")
                ->assertNotPresent("@record_data->test2_field2")
                ->assertPresent("@record_data->test2_field3");

            $browser->uncheck(".field-record-data-test-2-boolean input")
                ->pause(500)
                ->assertVisible(".component-form-nova-dependable-panel.field-test-2")
                ->assertPresent("@record_data->test2_field1")
                ->assertPresent("@record_data->test2_field2")
                ->assertNotPresent("@record_data->test2_field3");

            $browser->visit('about:blank')->waitForDialog()->acceptDialog();
        });
    }

    public function testValuesPersistMultipleRequests() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('tests'))
                ->waitFor(".field-record-data-test-1-hidden input")
                ->type("@record_data->test1_field1", "test1_field1")
                ->type("@record_data->test1_field2", "test1_field2")
                ->check(".field-record-data-test-1-hidden input")
                ->pause(500)
                ->uncheck(".field-record-data-test-1-hidden input")
                ->pause(500)
                ->assertInputValue("@record_data->test1_field1", "test1_field1")
                ->assertInputValue("@record_data->test1_field2", "test1_field2");
        });
    }

    public function testValuesPersistSingleRequest() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('tests'))
                ->waitFor(".field-record-data-test-2-boolean input")
                ->type("@record_data->test2_field1", "test2_field1")
                ->type("@record_data->test2_field2", "test2_field2")
                ->check(".field-record-data-test-2-boolean input")
                ->pause(500)
                ->uncheck(".field-record-data-test-2-boolean input")
                ->pause(500)
                ->assertInputValue("@record_data->test2_field1", "test2_field1")
                ->assertInputValue("@record_data->test2_field2", "test2_field2");
        });
    }

    public function testApplyToFields() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('tests'))
                ->waitFor(".field-record-data-test-3-boolean input")
                ->check(".field-record-data-test-3-boolean input")
                ->pause(500)
                ->assertDisabled("@record_data->test3_field1")
                ->assertDisabled("@record_data->test3_field2")
                ->assertEnabled("@record_data->test3_field3");

            $browser->waitFor(".field-record-data-test-4-boolean input")
                ->check(".field-record-data-test-4-boolean input")
                ->pause(500)
                ->assertDisabled("@record_data->test4_field1")
                ->assertDisabled("@record_data->test4_field2")
                ->assertEnabled("@record_data->test4_field3");
        });
    }

    public function testPanelIsSeparate() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('tests'))
                ->waitFor(".component-form-nova-dependable-panel.field-test-5")
                ->assertPresent(".component-form-panel.panel-test-5")
                ->assertPresent(".component-form-panel.panel-test-5 .component-form-nova-dependable-panel.field-test-5")
                ->assertPresent(".component-form-panel.panel-test-5 .component-form-nova-dependable-panel.field-test-5 .component-form-text-field.field-record-data-test-5-field-1 ");
        });
    }

    public function testSeparatePanelVisibility() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('tests'))
                ->waitFor(".component-form-nova-dependable-panel.field-test-5")
                ->assertVisible(".component-form-panel.panel-test-5")
                ->assertVisible(".component-form-panel.panel-test-5 .component-form-nova-dependable-panel.field-test-5")
                ->assertPresent(".component-form-panel.panel-test-5 .component-form-nova-dependable-panel.field-test-5 .component-form-text-field.field-record-data-test-5-field-1 ")
                ->check(".field-record-data-test-5-boolean input")
                ->pause(500)
                ->assertNotPresent(".component-form-panel.panel-test-5 .component-form-nova-dependable-panel.field-test-5 .component-form-text-field.field-record-data-test-5-field-1 ")
                ->assertMissing(".component-form-panel.panel-test-5 .component-form-nova-dependable-panel.field-test-5")
                ->assertMissing(".component-form-panel.panel-test-5");

                $browser
                ->waitFor(".component-form-nova-dependable-panel.field-test-6")
                ->assertVisible(".component-form-panel.panel-test-6")
                ->assertVisible(".component-form-panel.panel-test-6 .component-form-nova-dependable-panel.field-test-6")
                ->assertPresent(".component-form-panel.panel-test-6 .component-form-nova-dependable-panel.field-test-6 .component-form-text-field.field-record-data-test-6-field-1 ")
                ->check(".field-record-data-test-6-boolean input")
                ->pause(500)
                ->assertNotPresent(".component-form-panel.panel-test-6 .component-form-nova-dependable-panel.field-test-6 .component-form-text-field.field-record-data-test-6-field-1 ")
                ->assertMissing(".component-form-panel.panel-test-6 .component-form-nova-dependable-panel.field-test-6")
                ->assertMissing(".component-form-panel.panel-test-6");
        });
    }

    public function testValidation() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('tests'))
                ->waitFor(".component-form-nova-dependable-panel.field-test-7")
                ->waitFor(".component-form-nova-dependable-panel.field-test-8")
                ->click("@create-button")
                ->pause(500)
                ->assertSeeIn(".component-form-text-field.field-record-data-test-7-field-1", "The Field 1 field is required.")
                ->assertSeeIn(".component-form-text-field.field-record-data-test-8-field-1", "The Field 1 field is required.");

            $browser
                ->check(".field-record-data-test-7-boolean input")
                ->check(".field-record-data-test-8-boolean input")
                ->pause(500)
                ->assertDontSeeIn(".component-form-nova-dependable-panel.field-test-7", "The Field 1 field is required.")
                ->assertDontSeeIn(".component-form-nova-dependable-panel.field-test-8", "The Field 1 field is required.")
                ->click("@create-button")
                ->pause(500)
                ->assertSeeIn(".component-form-text-field.field-record-data-test-7-field-2", "The Field 2 field is required.")
                ->assertSeeIn(".component-form-text-field.field-record-data-test-8-field-2", "The Field 2 field is required.");
        });
    }

    public function testSubmission() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('tests'))
                ->waitFor(".component-form-nova-dependable-panel.field-test-7")
                ->waitFor(".component-form-nova-dependable-panel.field-test-8")
                ->type("@record_data->test7_field2", "test7_field1")
                ->check(".field-record-data-test-7-boolean input")
                ->type("@record_data->test7_field2", "test7_field2")
                ->type("@record_data->test8_field1", "test8_field1")
                ->create()
                ->waitForText("The test was created");

            $test = Test::orderBy('id', 'desc')->first();

            $this->assertSame(true, $test->record_data['test7_boolean']);
            $this->assertThat($test->record_data, $this->logicalNot($this->arrayHasKey('test7_field1')));
            $this->assertSame("test7_field2", $test->record_data['test7_field2']);
            $this->assertSame(false, $test->record_data['test8_boolean']);
            $this->assertSame("test8_field1", $test->record_data['test8_field1']);
        });
    }
}
