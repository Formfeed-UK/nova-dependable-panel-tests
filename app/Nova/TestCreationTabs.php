<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

use Formfeed\DependablePanel\DependablePanel;
use Formfeed\NovaFlexibleContent\Flexible;
use Formfeed\NovaFlexibleContent\Concerns\HasFlexibleDependsOn;

use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Tab;
use Eminiarts\Tabs\Traits\HasTabs;

class TestCreationTabs extends Resource {

    use HasTabs;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Test::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request) {
        return [
            Tabs::make(__("Test Tabs"), [
                Tab::make('Test Tab 1', [
                    Boolean::make("Test1 Boolean", "record_data->test1_boolean")->default(false),
                    DependablePanel::make("Test1 Panel", [
                        Text::make("Field 1", "record_data->test1_field1")
                            ->dependsOn(["record_data->test1_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                                if ($formData['record_data->test1_boolean'] == true) {
                                    $field->hide();
                                }
                            }),
                    ])->singleRequest(true),

                    Boolean::make("Test2 Boolean", "record_data->test2_boolean")->default(false),
                    DependablePanel::make("Test2 Panel", [
                        Text::make("Field 1", "record_data->test2_field1")
                            ->dependsOn(["record_data->test2_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                                if ($formData['record_data->test2_boolean'] == true) {
                                    $field->hide();
                                }
                            }),
                    ])->singleRequest(false),

                    Boolean::make("Test3 Boolean", "record_data->test3_boolean")->default(false),
                    DependablePanel::make("Test3 Panel", [
                        Text::make("Field 1", "record_data->test3_field1"),
                    ])->singleRequest(true)
                        ->dependsOn(["record_data->test3_boolean"], function (DependablePanel $field, NovaRequest $request, FormData $formData) {
                            if ($formData['record_data->test3_boolean'] == true) {
                                $field->hide();
                            }
                        }),

                    Boolean::make("Test4 Boolean", "record_data->test4_boolean")->default(false),
                    DependablePanel::make("Test4 Panel", [
                        Text::make("Field 1", "record_data->test4_field1")
                            ->rules("required"),
                    ])->singleRequest(true),

                    Boolean::make("Test5 Boolean", "record_data->test5_boolean")->default(false),
                    DependablePanel::make("Test5 Panel", [
                        Text::make("Field 1", "record_data->test5_field1")
                            ->dependsOn(["record_data->test5_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                                if ($formData['record_data->test5_boolean'] == true) {
                                    $field->rules("required");
                                }
                            }),
                    ])->singleRequest(true),

                    Boolean::make("Test6 Boolean", "record_data->test6_boolean")->default(false),
                    DependablePanel::make("Test6 Panel", [
                        Text::make("Field 1", "record_data->test6_field1")
                    ])->singleRequest(true)->separatePanel(true),
                    Boolean::make("Test7 Boolean", "record_data->test7_boolean")->default(false),

                ])->position(0),
                Tab::make('Test Tab 2', [
                    DependablePanel::make("Test7 Panel", [
                        Text::make("Field 1", "record_data->test7_field1")
                            ->dependsOn(["record_data->test7_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                                if ($formData['record_data->test7_boolean'] == true) {
                                    $field->hide();
                                }
                            }),
                        Text::make("Field 2", "record_data->test7_field2")
                            ->dependsOn(["record_data->test7_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                                if ($formData['record_data->test7_boolean'] != true) {
                                    $field->hide();
                                }
                                else {
                                    $field->rules("required");
                                }
                            }),
                    ])->singleRequest(true),
                ])->position(1),
            ])->withToolbar(true)->showTitle(true)->withSlug("tab")->rememberTabs(true),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request) {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request) {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request) {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request) {
        return [];
    }
}
