//This is Build in Laravel Feature for Incoming Request Validatin Form Validatin.
//I Will Use This for incoming Request Validation.

<?php

namespace App\Http\Request\Bookings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exception\HttpResponseException;

class BookingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [];

        switch ($this->method()){
            case "POST" : {
                $rules +={
                    //Rules for POST Request Regard Booking
                }
            }
            case "PUT" : {
                $rules +={
                    //Rules For PUT Request Goes Here Regard Booking
                }
            }
            default:
                break;
        }

        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data'    => $validator->errors()
        ], 422));
    }
}