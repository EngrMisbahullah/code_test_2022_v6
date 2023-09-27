<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

//This code is Laravel Framework Code Consider use statment form App
use App\Http\Requests\Bookings\BookingRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */

    //Error Handling in Over the Project is not Properly Managed and also Exception error is not handled.
    public function index(Request $request)
    {
        
        try{
            // if __authenticatedUser->user_type  checking authenticated user type then we can it Directly from Auth Facades
            // No Need to Pass in Request the id things.\
            //Also if Role based Authenticatin is Done through Spatie Package then just i will create a function in modal of user role to check
            //User.php 
            /*
            public function isAdmin()
            {
                return $this->role_id === Role::ADMIN;
            }
            public function isSuperAdmin()
            {
                return $this->role_id === Role::SUPERADMIN;
            }
            */
            $user = Auth::user();
            $requestData = $request->all();
            if($user->isAdmin() || $user->isSuperAdmin()) {
                $response = $this->repository->getAll($requestData);
            }else{
                $response = $this->repository->getUsersJobs($user->id);
            }
            return response($response);

        }catch(\Throwable $e)
        {
            return response(false,[],$e->getMessage(),500);
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try{
            $job = $this->repository->with('translatorJobRel.user')->find($id);
            return response($job);

        }catch(\Throwable $e)
        {
            return response(false,[],$e->getMessage(),500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(BookingRequest $request)
    {
        try{
            $data = $request->all();
            $response = $this->repository->store($request->__authenticatedUser, $data);
            return response($response);
        }catch(\Throwable $e)
        {
            return response(false,[],$e->getMessage(),500);
        }

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, BookingRequest $request)
    {
        try{
            $data = $request->all();
            $cuser = $request->__authenticatedUser;
            $response = $this->repository->updateJob($id, array_except($data, ['_token', 'submit']), $cuser);
    
            return response($response);
        }catch(\Throwable $e)
        {
            return response(false,[],$e->getMessage(),500);
        }
        
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        try{
            $adminSenderEmail = config('app.adminemail');
            $data = $request->all();
    
            $response = $this->repository->storeJobEmail($data);
    
            return response($response);
        }catch(\Throwable $e)
        {
            return response(false,[],$e->getMessage(),500);
        }
        
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        try{
            if($user_id = $request->get('user_id')) {
                $response = $this->repository->getUsersJobsHistory($user_id, $request);
                return response($response);
            }
            return null;
        }catch(\Throwable $e)
        {
            return response(false,[],$e->getMessage(),500);
        }
        
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        try{

            $data = $request->all();
            $user = $request->__authenticatedUser;
            $response = $this->repository->acceptJob($data, $user);
            
            return response($response);

        }catch(\Throwable $e)
        {
            return response(false,[],$e->getMessage(),500);
        }
        
    }

    public function acceptJobWithId(Request $request)
    {

        try{
            $data = $request->get('job_id');
            $user = $request->__authenticatedUser;
            $response = $this->repository->acceptJobWithId($data, $user);
            return response($response);

        }catch(\Throwable $e)
        {
            return response(false,[],$e->getMessage(),500);
        }
        
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {

        try{
            
            $data = $request->all();
            $user = $request->__authenticatedUser;
            $response = $this->repository->cancelJobAjax($data, $user);
            return response($response);

        }catch(\Throwable $e)
        {
            return response(false,[],$e->getMessage(),500);
        }
        
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        try{
            $data = $request->all();
            $response = $this->repository->endJob($data);
            return response($response);
        }catch(\Throwable $e)
        {
            return response(false,[],$e->getMessage(),5);
        }
        

    }

    public function customerNotCall(Request $request)
    {
        $data = $request->all();
        $response = $this->repository->customerNotCall($data);
        return response($response);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $this->repository->getPotentialJobs($user);

        return response($response);
    }

    public function distanceFeed(Request $request)
    {
        $data = $request->all();

        $distance = $data['distance'] ?? "";
        $time = $data['time'] ?? "";
        $jobid = $data['jobid'] ?? "";
        $session = $data['session_time'] ?? "";
        
        $flagged = $data['flagged'] == 'true' && empty($data['admincomment']) ? 'yes' : 'no';
        $manually_handled = $data['manually_handled'] == 'true' ? 'yes' : 'no';
        $by_admin = $data['by_admin'] == 'true' ? 'yes' : 'no';

        $admincomment = $data['admincomment'] ?? "";

        if ($time || $distance) {
            Distance::where('job_id', '=', $jobid)->update(['distance' => $distance, 'time' => $time]);
        }

        if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {
            Job::where('id', '=', $jobid)->update([
                'admin_comments' => $admincomment,
                'flagged' => $flagged,
                'session_time' => $session,
                'manually_handled' => $manually_handled,
                'by_admin' => $by_admin
            ]);
        }

        return response('Record updated!');
    }

    public function reopen(Request $request)
    {
        $data = $request->all();
        $response = $this->repository->reopen($data);

        return response($response);
    }

    public function resendNotifications(Request $request)
    {
        $data = $request->all();
        $job = $this->repository->find($data['jobid']);
        $job_data = $this->repository->jobToData($job);
        $this->repository->sendNotificationTranslator($job, $job_data, '*');

        return response(['success' => 'Push sent']);
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        $data = $request->all();
        $job = $this->repository->find($data['jobid']);
        $job_data = $this->repository->jobToData($job);

        try {
            $this->repository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }

}
