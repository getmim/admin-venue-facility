<?php
/**
 * FacilityController
 * @package admin-venue-facility
 * @version 0.0.1
 */

namespace AdminVenueFacility\Controller;

use LibFormatter\Library\Formatter;
use LibForm\Library\Form;
use LibPagination\Library\Paginator;
use VenueFacility\Model\{
    VenueFacility as VFacility,
    VenueFacilityChain as VFChain
};

class FacilityController extends \Admin\Controller
{
    private function getParams(string $title): array{
        return [
            '_meta' => [
                'title' => $title,
                'menus' => ['venue', 'facility']
            ],
            'subtitle' => $title,
            'pages' => null
        ];
    }

    public function editAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_venue_facility)
            return $this->show404();

        $facility = (object)[];

        $id = $this->req->param->id;
        if($id){
            $facility = VFacility::getOne(['id'=>$id]);
            if(!$facility)
                return $this->show404();
            $params = $this->getParams('Edit Venue Facility');
        }else{
            $params = $this->getParams('Create New Venue Facility');
        }

        $form           = new Form('admin.venue-facility.edit');
        $params['form'] = $form;

        if(!($valid = $form->validate($facility)) || !$form->csrfTest('noob'))
            return $this->resp('venue/facility/edit', $params);

        if($id){
            if(!VFacility::set((array)$valid, ['id'=>$id]))
                deb(VFacility::lastError());
        }else{
            $valid->user = $this->user->id;
            if(!VFacility::create((array)$valid))
                deb(VFacility::lastError());
        }

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => $id ? 2 : 1,
            'type'   => 'venue-facility',
            'original' => $facility,
            'changes'  => $valid
        ]);

        $next = $this->router->to('adminVenueFacility');
        $this->res->redirect($next);
    }

    public function indexAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_venue_facility)
            return $this->show404();

        $cond = $pcond = [];
        if($q = $this->req->getQuery('q'))
            $pcond['q'] = $cond['q'] = $q;

        list($page, $rpp) = $this->req->getPager(25, 50);

        $facilities = VFacility::get($cond, $rpp, $page, ['name'=>true]) ?? [];
        if($facilities)
            $facilities = Formatter::formatMany('venue-facility', $facilities, ['user']);

        $params               = $this->getParams('Venue Facility');
        $params['facilities'] = $facilities;
        $params['form']       = new Form('admin.venue-facility.index');

        $params['form']->validate( (object)$this->req->get() );

        // pagination
        $params['total'] = $total = VFacility::count($cond);
        if($total > $rpp){
            $params['pages'] = new Paginator(
                $this->router->to('adminVenueFacility'),
                $total,
                $page,
                $rpp,
                10,
                $pcond
            );
        }

        $this->resp('venue/facility/index', $params);
    }

    public function removeAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_venue_facility)
            return $this->show404();

        $id       = $this->req->param->id;
        $facility = VFacility::getOne(['id'=>$id]);
        $next     = $this->router->to('adminVenueFacility');
        $form     = new Form('admin.venue-facility.index');

        if(!$facility)
            return $this->show404();

        if(!$form->csrfTest('noob'))
            return $this->res->redirect($next);

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => 3,
            'type'   => 'venue-facility',
            'original' => $facility,
            'changes'  => null
        ]);

        VFacility::remove(['id'=>$id]);
        VFChain::remove(['facility'=>$id]);
        
        $this->res->redirect($next);
    }
}