<?php

namespace App\Providers;

use App\Repositories\Permissions\ModuleRepository;
use Gomee\Html\Input;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * @param Input $input
         */
        Input::addTemplate('checktree', ['checkbox', 'checktree'], function ($input) {
            // do st
            // $input->optio

        });

        Input::addTemplate('module-table', ['checklist', 'checkbox', 'moduletable', 'module-table'], function (Input $input) {
            $input->addClass('module-table permision-matrix');
            $scope = $input->hidden('scope')??'admin';
            $m = app(ModuleRepository::class)->getModuleMatrix($scope);
            $input->hiddenData('modules', $m);
            $input->hidden('__id__', $input->getParamFromString("#hidden_id"));
        });

        Input::addTemplate('coloris', ['coloris', 'color', 'text'], function (Input $input) {
            // $input->addClass('coloris');
            $input->addClass('coloris');
        });
        add_js_data('coloris_swatches', [
            '#264653',
            '#2a9d8f',
            '#e9c46a',
            '#f4a261',
            '#e76f51',
            '#d62828',
            '#023e8a',
            '#0077b6',
            '#0096c7',
            '#00b4d8',
            '#48cae4'
        ]);

        Input::addTemplate('style-item-preview-config', ['style-item-preview-config', 'array'], function (Input $input) {
            //
            // $input->addClass('preview-config');
        });

        Input::addTemplate('module-exams-table', ['module-exams-table'], function (Input $input) {
            $input->addClass('module-exams-table');
        });

        Input::addTemplate('product', ['product'], function (Input $input) {
            $input->addClass('product');
        });

        Input::addTemplate('export-exams', ['export-exams'], function (Input $input) {
            $input->addClass('export-exams');
        });


        Input::addTemplate('post-config', ['post-config', 'textarea'], function (Input $input) {
            $input->addClass('post-config-wrapper');
        });

        Input::addTemplate('articles', ['articles', 'post-list'], function (Input $input) {
            $input->addClass('input-article-wrapper');
        });

        Input::addTemplate('award-items', ['award-items', 'awarditemd'], function (Input $input) {
            $input->addClass('input-award-items-wrapper');
        });



        Input::addTemplate('structure', ['structure'], function (Input $input) {
            $input->addClass('structure-wrapper');
        });

        Input::addTemplate('level-labels', ['level-labels', 'textarea'], function (Input $input) {
        });

        Input::addTemplate('ci-scan', ['ci-scan', 'ciscan', 'file', 'image'], function (Input $input) {
            $input->addClass('custom-file-input');
        });

        Input::addTemplate('range-slider', ['range-slider', 'range'], function (Input $input) {
            $input->addClass('range-slider-input');
        });
        Input::addTemplate('place', ['place', 'text'], function (Input $input) {
            $input->addClass('place-input');
        });

        Input::addTemplate('modelpreview', ['modelpreview', 'text'], function (Input $input) {
            $input->addClass('place-input');
        });

        Input::addTemplate('3dpreview', ['3dpreview', 'text'], function (Input $input) {
            $input->addClass('3dpreview-input');
        });

        Input::addTemplate('radopts', ['options', 'radopts', 'radio'], function (Input $input) {

            $data = $input->getInputData();
            $input->set('data', $data);
            if (!is_array($dd = $input->get('data_docs')) || !$dd) {

                if (($c = $input->get('doc_call') ?? $input->get('data_docs')) && is_callable($c)) {

                    $p = $input->parseInputParams($input->get('doc_params') ?? $input->get('data_doc_params'));
                    $data = call_user_func_array($c, $p);
                    $input->set('data_docs', $data);
                }
            }
        });
        Input::addTemplate('optlist', ['optlist', 'radio'], function (Input $input) {

            $data = $input->getInputData();
            $input->set('data', $data);
            $defVal = $input->defVal();
            if(!$defVal && $defVal!==0){
                if(is_array($data)){
                    foreach ($data as $value => $text) {
                        $defVal = $value;
                        break;
                    }
                }
            }
            $input->val($defVal);

        });


        Input::addTemplate('ai-prompt', ['ai-prompt', 'textarea'], function (Input $input) {
            $input->addClass('ai-prompt-editor');
        });

    }
}
