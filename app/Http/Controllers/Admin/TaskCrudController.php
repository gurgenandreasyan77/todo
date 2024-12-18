<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class TaskCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TaskCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Task::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/task');
        CRUD::setEntityNameStrings('task', 'tasks');
    }


    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setFromDb();

        $user = backpack_user();

        if (!$user->is_admin) {
            CRUD::addClause('where', 'user_id', '=', $user->id);
        }

        CRUD::column('name');
        CRUD::column('description');
        CRUD::column('status')->type('select_from_array')->options([
            'new' => 'New',
            'pending' => 'Pending',
            'completed' => 'Completed',
        ]);

        CRUD::column('user_id')->label('Created By');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(TaskRequest::class);
        CRUD::addField('name');
        CRUD::addField('description');
        CRUD::addField([
            'name'    => 'status',
            'label'   => 'Status',
            'type'    => 'select_from_array',
            'options' => [
                'new' => 'New',
                'pending' => 'Pending',
                'completed' => 'Completed',
            ],
        ]);

        CRUD::addField([
            'name' => 'user_id',
            'type' => 'hidden',
            'default' => backpack_user()->id,
        ]);

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }

    protected function setupUpdateOperation()
    {
        $taskId = request()->route('id'); // Get the task ID from the request route
        $task = Task::find($taskId);

        if ($this->canUpdateOrDelete($task)) {
            $this->setupCreateOperation();
        } else {
            abort(403, 'Unauthorized access.'); // Forbid access
        }
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupDeleteOperation()
    {
        $taskId = request()->route('id');
        $task = Task::find($taskId);

        if (!$this->canUpdateOrDelete($task)) {
            abort(403, 'Unauthorized access.'); // Forbid access
        }
    }

    private function canUpdateOrDelete($task)
    {
        if (!$task) {
            return false;
        }

        $user = backpack_user();
        return $user->is_admin || $user->id === $task->user_id;
    }

}
