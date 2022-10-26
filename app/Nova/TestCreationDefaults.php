<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Http\Requests\NovaRequest;

use Formfeed\DependablePanel\DependablePanel;
use Formfeed\NovaFlexibleContent\Flexible;
use Formfeed\NovaFlexibleContent\Concerns\HasFlexibleDependsOn;

class TestCreationDefaults extends Resource {
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
            ID::make()->sortable(),

            Boolean::make("Test1 Boolean", "record_data->test1_boolean")->default(false),
            DependablePanel::make('Test1', [
                Text::make('Field 1', "record_data->test1_field1")->default('test1_field1_default'),
                Text::make('Field 2', "record_data->test1_field2")->default('test1_field2_default'),
            ])
                ->dependsOn(["record_data->test1_boolean"], function (DependablePanel $panel, NovaRequest $request, FormData $formData) {
                    if ($formData['record_data->test1_boolean'] == true) {
                        $panel->hide();
                    }
                }),

            Boolean::make("Test2 Boolean", "record_data->test2_boolean")->default(false),
            DependablePanel::make('Test2', [
                Text::make('Field 1', "record_data->test2_field1")
                    ->default("test2_field1_default")
                    ->dependsOn(["record_data->test2_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test2_boolean'] == true) {
                            $field->hide();
                        }
                    }),
                Text::make('Field 2', "record_data->test2_field2")
                    ->default("test2_field2_default")
                    ->dependsOn(["record_data->test2_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test2_boolean'] == true) {
                            $field->hide();
                        }
                    }),
                Text::make('Field 3', "record_data->test2_field3")
                    ->default("test2_field3_default")
                    ->dependsOn(["record_data->test2_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test2_boolean'] != true) {
                            $field->hide();
                        }
                    }),
            ])->singleRequest(true),

            Boolean::make("Test3 Boolean", "record_data->test3_boolean")->default(false),
            DependablePanel::make('Test3', [
                Text::make('Field 1', "record_data->test3_field1")
                    ->default("test3_field1_default")
                    ->dependsOn(["record_data->test3_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test3_boolean'] == true) {
                            $field->hide();
                        }
                    }),
                Text::make('Field 2', "record_data->test3_field2")
                    ->default("test3_field2_default")
                    ->dependsOn(["record_data->test3_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test3_boolean'] == true) {
                            $field->hide();
                        }
                    }),
                Text::make('Field 3', "record_data->test3_field3")
                    ->default("test3_field3_default")
                    ->dependsOn(["record_data->test3_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test3_boolean'] != true) {
                            $field->hide();
                        }
                    }),
            ])->singleRequest(false),

            Boolean::make("Test4 Boolean", "record_data->test4_boolean")->default(false),
            DependablePanel::make('Test4', [
                Text::make('Field 1', "record_data->test4_field1")
                    ->default("test4_field1_default")
                    ->dependsOn(["record_data->test4_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test4_boolean'] == true) {
                            $field->value = "test4_field1_changed";
                        }
                    }),
            ])->singleRequest(true),

            Boolean::make("Test5 Boolean", "record_data->test5_boolean")->default(false),
            DependablePanel::make('Test5', [
                Text::make('Field 1', "record_data->test5_field1")
                    ->default("test5_field1_default")
                    ->dependsOn(["record_data->test5_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test5_boolean'] == true) {
                            $field->value = "test5_field1_changed";
                        }
                    }),
            ])->singleRequest(false),

            Boolean::make("Test6 Boolean", "record_data->test6_boolean")->default(false),
            DependablePanel::make('Test6', [
                Text::make('Field 1', "record_data->test6_field1")
                    ->default("test6_field1_default"),
                Text::make('Field 2', "record_data->test6_field2")
                    ->default("test6_field2_default")
                    ->dependsOn(["record_data->test6_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test6_boolean'] == true) {
                            $field->value = "test6_field2_updated";
                        }
                    }),
            ])->singleRequest(false)
                ->dependsOn(["record_data->test6_boolean"], function (DependablePanel $panel, NovaRequest $request, FormData $formData) {
                    $panel->applyToFields(function (Field $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test6_boolean'] == true) {
                            $field->value = "test6_general_updated";
                        }
                    });
                }),

            Boolean::make("Test7 Boolean", "record_data->test7_boolean")->default(false),
            DependablePanel::make('Test7', [
                Text::make('Field 1', "record_data->test7_field1")
                    ->default("test7_field1_default"),
                Text::make('Field 2', "record_data->test7_field2")
                    ->default("test7_field2_default")
                    ->dependsOn(["record_data->test7_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test7_boolean'] == true) {
                            $field->value = "test7_field2_updated";
                        }
                    }),
            ])->singleRequest(false)
                ->dependsOn(["record_data->test7_boolean"], function (DependablePanel $panel, NovaRequest $request, FormData $formData) {
                    $panel->applyToFields(function (Field $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test7_boolean'] == true) {
                            $field->value = "test7_general_updated";
                        }
                    });
                }),

            Boolean::make("Test8 Boolean", "record_data->test8_boolean")->default(false),
            DependablePanel::make('Test8', [
                Text::make('Field 1', "record_data->test8_field1")
                    ->default("test8_field1_default"),
                Text::make('Field 2', "record_data->test8_field2")
                    ->default("test8_field2_default")
            ])->singleRequest(true)
                ->dependsOn(["record_data->test8_boolean"], function (DependablePanel $panel, NovaRequest $request, FormData $formData) {
                    if ($formData['record_data->test8_boolean'] == true) {
                        $panel->fields([
                            Text::make('Field 3', "record_data->test8_field3")
                                ->default("test8_field3_default"),
                            Text::make('Field 4', "record_data->test8_field4")
                                ->default("test8_field4_default")
                        ]);
                    }
                }),

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
