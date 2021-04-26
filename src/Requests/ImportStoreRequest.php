<?php

namespace OZiTAG\Tager\Backend\Import\Requests;

use App\Enums\FileScenario;
use Ozerich\FileStorage\Rules\FileRule;
use OZiTAG\Tager\Backend\Core\Http\FormRequest;
use OZiTAG\Tager\Backend\Crud\Requests\CrudFormRequest;
use OZiTAG\Tager\Backend\Import\TagerImport;

/**
 * Class ImportStoreRequest
 * @package OZiTAG\Tager\Backend\Import\Requests
 *
 * @property string $strategy
 * @property string $file
 * @property string $delimiter
 */
class ImportStoreRequest extends CrudFormRequest
{
    public function fileScenarios()
    {
        return [
            'file' => TagerImport::getFileScenario()
        ];
    }

    public function rules()
    {
        return [
            'strategy' => 'required|string',
            'file' => ['required', new FileRule()],
            'delimiter' => 'nullable|string'
        ];
    }
}
