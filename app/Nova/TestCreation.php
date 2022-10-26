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

class TestCreation extends Resource {
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

            Boolean::make("Test1 Boolean", "record_data->test1_hidden")->default(false),
            DependablePanel::make('Test1', [
                Text::make('Field 1', "record_data->test1_field1"),
                Text::make('Field 2', "record_data->test1_field2"),
            ])
                ->dependsOn(["record_data->test1_hidden"], function (DependablePanel $panel, NovaRequest $request, FormData $formData) {
                    if ($formData['record_data->test1_hidden'] == true) {
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
                        if ($formData['record_data->test2_boolean'] == true) {
                            $field->hide();
                        }
                    }),
                Text::make('Field 3', "record_data->test2_field3")
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

            Boolean::make("Test6 Boolean", "record_data->test6_boolean")->default(false),
            DependablePanel::make('Test6', [
                Text::make('Field 1', "record_data->test6_field1")
                    ->dependsOn(["record_data->test6_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test6_boolean'] == true) {
                            $field->hide();
                        }
                    })
            ])
                ->singleRequest(false)->separatePanel(true),

            Boolean::make("Test7 Boolean", "record_data->test7_boolean")->default(false),
            DependablePanel::make('Test7', [
                Text::make('Field 1', "record_data->test7_field1")
                    ->rules("required")
                    ->dependsOn(["record_data->test7_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test7_boolean'] == true) {
                            $field->hide();
                            $field->rules([]);
                        }
                    }),
                Text::make('Field 2', "record_data->test7_field2")
                    ->dependsOn(["record_data->test7_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test7_boolean'] == true) {
                            $field->rules("required");
                        }
                    })
            ])
                ->singleRequest(true)->separatePanel(false),

            Boolean::make("Test8 Boolean", "record_data->test8_boolean")->default(false),
            DependablePanel::make('Test8', [
                Text::make('Field 1', "record_data->test8_field1")
                    ->rules("required")
                    ->dependsOn(["record_data->test8_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test8_boolean'] == true) {
                            $field->rules([]);
                            $field->hide();
                        }
                    }),
                Text::make('Field 2', "record_data->test8_field2")
                    ->dependsOn(["record_data->test8_boolean"], function (Text $field, NovaRequest $request, FormData $formData) {
                        if ($formData['record_data->test8_boolean'] == true) {
                            $field->rules("required");
                        }
                    })
            ])
                ->singleRequest(false)->separatePanel(false),

            Boolean::make("Test9 Boolean", "record_data->test9_boolean")->default(false),
            DependablePanel::make('Test9', [
                Text::make('Field 1', "record_data->test9_field1"),
                Text::make('Field 2', "record_data->test9_field2"),
            ])
                ->singleRequest(true)
                ->dependsOn(["record_data->test9_boolean"], function (DependablePanel $panel, NovaRequest $request, FormData $formData) {
                    if ($formData['record_data->test9_boolean'] == true) {
                        $panel->fields([
                            Text::make('Field 3', "record_data->test9_field3"),
                            Text::make('Field 4', "record_data->test9_field4"),
                        ]);
                    }
                }),

            Boolean::make("Test10 Boolean", "record_data->test10_boolean")->default(false),
            Boolean::make("Test10 Boolean 2", "record_data->test10_boolean2")->default(false),
            DependablePanel::make('Test10', [
                Text::make('Field 1', "record_data->test10_field1"),
                Text::make('Field 2', "record_data->test10_field2"),
            ])
                ->singleRequest(true)
                ->dependsOn(["record_data->test10_boolean", "record_data->test10_boolean2"], function (DependablePanel $panel, NovaRequest $request, FormData $formData) {
                    if ($formData['record_data->test10_boolean'] == true) {
                        $panel->fields([
                            Text::make('Field 3', "record_data->test10_field3")
                                ->dependsOn(["record_data->test10_boolean2"], function (Text $field, NovaRequest $request, FormData $formData) {
                                    if ($formData['record_data->test10_boolean2'] == true) {
                                        $field->readonly(true);
                                    }
                                }),
                            Text::make('Field 4', "record_data->test10_field4")
                                ->dependsOn(["record_data->test10_boolean2"], function (Text $field, NovaRequest $request, FormData $formData) {
                                    if ($formData['record_data->test10_boolean2'] == true) {
                                        $field->rules("required");
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
