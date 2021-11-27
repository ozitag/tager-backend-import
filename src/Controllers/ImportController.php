<?php

namespace OZiTAG\Tager\Backend\Import\Controllers;

use OZiTAG\Tager\Backend\Crud\Actions\IndexAction;
use OZiTAG\Tager\Backend\Crud\Actions\StoreOrUpdateAction;
use OZiTAG\Tager\Backend\Crud\Controllers\AdminCrudController;
use OZiTAG\Tager\Backend\Import\Enums\ImportSessionStatus;
use OZiTAG\Tager\Backend\Import\Features\ImportInfoFeature;
use OZiTAG\Tager\Backend\Import\Models\ImportSession;
use OZiTAG\Tager\Backend\Import\Operations\CreateImportOperation;
use OZiTAG\Tager\Backend\Import\Repositories\ImportSessionRepository;
use OZiTAG\Tager\Backend\Import\Requests\ImportStoreRequest;

class ImportController extends AdminCrudController
{
    public bool $hasCountAction = false;

    public bool $hasDeleteAction = false;

    public bool $hasUpdateAction = false;

    public function __construct(ImportSessionRepository $repository)
    {
        parent::__construct($repository);

        $this->setIndexAction((new IndexAction())->disableSearchByQuery());

        $this->setStoreAction(new StoreOrUpdateAction(
            ImportStoreRequest::class,
            CreateImportOperation::class
        ));

        $this->setResourceFields([
            'id', 'strategy',
            'status:enum:' . ImportSessionStatus::class,
            'message',
            'params:json',
            'history' => function (ImportSession $importSession) {
                $result = [
                    [
                        'status' => ImportSessionStatus::label(ImportSessionStatus::Created),
                        'datetime' => $importSession->created_at,
                    ]
                ];

                if ($importSession->started_at) {
                    $result[] = [
                        'status' => ImportSessionStatus::label(ImportSessionStatus::Validation),
                        'datetime' => $importSession->started_at,
                    ];
                }

                if ($importSession->validated_at) {
                    $result[] = [
                        'status' => ImportSessionStatus::label(ImportSessionStatus::InProgress),
                        'datetime' => $importSession->validated_at,
                    ];
                }

                if ($importSession->completed_at) {
                    $result[] = [
                        'status' => ImportSessionStatus::label($importSession->status),
                        'datetime' => $importSession->completed_at,
                    ];
                }

                return $result;
            },
            'file' => function (ImportSession $importSession) {
                return $importSession->file ? $importSession->file->getShortJson() : null;
            },
        ], true);
    }

    public function info()
    {
        return $this->serve(ImportInfoFeature::class);
    }
}
