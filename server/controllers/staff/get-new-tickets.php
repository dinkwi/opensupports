<?php
use RedBeanPHP\Facade as RedBean;
use Respect\Validation\Validator as DataValidator;

/**
 * @api {post} /staff/get-new-tickets  Get new tickets.
 *
 * @apiName Get new tickets
 *
 * @apiGroup staff
 *
 * @apiDescription This path give back new tickets.
 *
 * @apiPermission Staff level 1
 *
 * @apiSuccess {Object} data
 *
 */

class GetNewTicketsStaffController extends Controller {
    const PATH = '/get-new-tickets';
    const METHOD = 'POST';

    public function validations() {
        return[
            'permission' => 'staff_1',
            'requestData' => []
        ];
    }
    public function handler() {
        if (Ticket::isTableEmpty()) {
            Response::respondSuccess([]);
            return;
        }

        $user = Controller::getLoggedUser();
        $query = ' (';
        foreach ($user->sharedDepartmentList as $department) {
            $query .= 'department_id=' . $department->id . ' OR ';
        }
        $query = substr($query,0,-3);
        $ownerExists = RedBean::exec('SHOW COLUMNS FROM ticket LIKE \'owner_id\'');

        if($ownerExists != 0) {
            $query .= ') AND owner_id IS NULL';
        } else {
            $query .= ')';
        }

        $ticketList = Ticket::find($query);

        Response::respondSuccess($ticketList->toArray());
    }
}
