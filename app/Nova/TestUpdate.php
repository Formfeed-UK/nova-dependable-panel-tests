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

class TestUpdate extends Resource {
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
                Text::make('Field 1', "record_data->test1_field1"),
                Text::make('Field 2', "record_data->test1_field2"),
            ])
                ->dependsOn(["record_data->test1_boolean"], function (DependablePanel $panel, NovaRequest $request, FormData $formData) {
                    if ($formData['record_data->test1_boolean'] == true) {
                        $panel->hide();
                    }
                }),

            Boolean::make("Test2 Boolean", "record_data->test2_boolean")->default(false),
            DependablePanel::make('Test2', [
                Text::make('Field 1', "record_data->test2_field1")
                    ->dependsOn(["record_data->test2_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test2_boolean'] == true) {
                            $field->hide();
                        }
                    }),
                Text::make('Field 2', "record_data->test2_field2")
                    ->dependsOn(["record_data->test2_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test2_boolean'] != true) {
                            $field->hide();
                        }
                    }),
            ])
                ->singleRequest(true),

            Boolean::make("Test3 Boolean", "record_data->test3_boolean")->default(false),
            DependablePanel::make('Test3', [
                Text::make('Field 1', "record_data->test3_field1"),
                Text::make('Field 2', "record_data->test3_field2"),
                Text::make('Field 3', "record_data->test3_field3")
                    ->dependsOn(["record_data->test3_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test3_boolean'] == true) {
                            $field->readonly(false);
                            $field->withMeta(['extraAttributes' => ['readonly' => false]]);
                        }
                    }),
            ])
                ->singleRequest(true)
                ->dependsOn(["record_data->test3_boolean"], function (DependablePanel $panel, NovaRequest $request, FormData $formData) {
                    $panel->applyToFields(function (Field $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test3_boolean'] == true) {
                            $field->readonly(true);
                        }
                    });
                }),

            Boolean::make("Test4 Boolean", "record_data->test4_boolean")->default(false),
            DependablePanel::make('Test4', [
                Text::make('Field 1', "record_data->test4_field1"),
                Text::make('Field 2', "record_data->test4_field2"),
                Text::make('Field 3', "record_data->test4_field3")
                    ->dependsOn(["record_data->test4_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test4_boolean'] == true) {
                            $field->readonly(false);
                            $field->withMeta(['extraAttributes' => ['readonly' => false]]);
                        }
                    }),
            ])
                ->singleRequest(false)
                ->dependsOn(["record_data->test4_boolean"], function (DependablePanel $panel, NovaRequest $request, FormData $formData) {
                    $panel->applyToFields(function (Field $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test4_boolean'] == true) {
                            $field->readonly(true);
                        }
                    });
                }),

            Boolean::make("Test5 Boolean", "record_data->test5_boolean")->default(false),
            DependablePanel::make('Test5', [
                Text::make('Field 1', "record_data->test5_field1")
                    ->dependsOn(["record_data->test5_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test5_boolean'] == true) {
                            $field->hide();
                        }
                    })
            ])
                ->singleRequest(true)->separatePanel(true),

            //Text::make('Field 1 Out', "record_data->test7_out_field1")->rules('min:2'),

            Boolean::make("Test7 Boolean", "record_data->test7_boolean")->default(false),
            DependablePanel::make('Test7', [
                Text::make('Field 1', "record_data->test7_field1")
                    ->rules("min:2")
                    ->dependsOn(["record_data->test7_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test7_boolean'] == true) {
                            $field->hide();
                            $field->rules([]);
                        }
                    }),
                Text::make('Field 2', "record_data->test7_field2")
                    ->dependsOn(["record_data->test7_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test7_boolean'] == true) {
                            $field->rules("min:2");
                        }
                    })
            ])
                ->singleRequest(true)->separatePanel(false),

            Boolean::make("Test8 Boolean", "record_data->test8_boolean")->default(false),
            DependablePanel::make('Test8', [
                Text::make('Field 1', "record_data->test8_field1")
                    ->rules("min:2")
                    ->dependsOn(["record_data->test8_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test8_boolean'] == true) {
                            $field->rules([]);
                            $field->hide();
                        }
                    }),
                Text::make('Field 2', "record_data->test8_field2")
                    ->dependsOn(["record_data->test8_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test8_boolean'] == true) {
                            $field->rules("min:2");
                        }
                    })
            ])->singleRequest(false)->separatePanel(false),

            Boolean::make("Test9 Boolean", "record_data->test9_boolean")->default(false),
            DependablePanel::make('Test9', [
                Text::make('Field 1', "record_data->test9_field1")
                    ->hide()
                    ->dependsOn(["record_data->test9_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test9_boolean'] == true) {
                            $field->show();
                        }
                    }),
                Text::make('Field 2', "record_data->test9_field2")
            ])->singleRequest(false)->separatePanel(false),

            Boolean::make("Test10 Boolean", "record_data->test10_boolean")->default(false),
            DependablePanel::make('Test10', [
                Text::make('Field 1', "record_data->test10_field1")
                    ->hide()
                    ->dependsOn(["record_data->test10_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test10_boolean'] == true) {
                            $field->show();
                        }
                    }),
                Text::make('Field 2', "record_data->test10_field2")
            ])->singleRequest(true)->separatePanel(false),

            Boolean::make("Test11 Boolean", "record_data->test11_boolean")->default(false),
            DependablePanel::make('Test11', [
                Text::make('Field 1', "record_data->test11_field1")
                    ->dependsOn(["record_data->test11_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test11_boolean'] == true) {
                            $field->readonly(true);
                        }
                    }),
            ])->singleRequest(true)->separatePanel(false),

            Boolean::make("Test12 Boolean", "record_data->test12_boolean")->default(false),
            DependablePanel::make('Test12', [
                Text::make('Field 1', "record_data->test12_field1"),
            ])
                ->singleRequest(true)
                ->dependsOn(["record_data->test12_boolean"], function (DependablePanel $panel, NovaRequest $request, FormData $formData) {
                    if ($formData['record_data->test12_boolean'] == true) {
                        $panel->fields([
                            Text::make('Field 3', "record_data->test12_field2"),
                        ]);
                    }
                }),

            Boolean::make("Test13 Boolean", "record_data->test13_boolean")->default(false),
            Boolean::make("Test13 Boolean 2", "record_data->test13_boolean2")->default(false),
            DependablePanel::make('Test13', [
                Text::make('Field 1', "record_data->test13_field1"),
            ])
                ->singleRequest(true)
                ->dependsOn(["record_data->test13_boolean", "record_data->test13_boolean2"], function (DependablePanel $panel, NovaRequest $request, FormData $formData) {
                    if ($formData['record_data->test13_boolean'] == true) {
                        $panel->fields([
                            Text::make('Field 3', "record_data->test13_field2")
                                ->dependsOn(["record_data->test13_boolean2"], function (Text $field, NovaRequest $request, FormData $formData) {
                                    if ($formData['record_data->test13_boolean2'] == true) {
                                        $field->readonly(true);
                                    }
                                }),
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
