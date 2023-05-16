<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class JsonController extends Controller
{

    public function index(Request $request)
    {
        $path = public_path('json\file1.json');
        $dataJsonUser = json_decode(file_get_contents($path), true);

        $loop = [];

        foreach ($dataJsonUser['data'] as $value) {
            
            $codeWorkshop = $value['booking']['workshop']['code'];
            
            $path = public_path('json\file2.json');
            $dataJsonPlace = json_decode(file_get_contents($path), true);

            $getCode = array_filter($dataJsonPlace['data'], function($code) use($codeWorkshop) {
                return $code['code'] == $codeWorkshop;
            });
            
            $results = array_shift($getCode);

            $data = [
                "name" => $value['name'],
                "email"=> $value['email'],
                "booking_number"=> $value['booking']['booking_number'],
                "book_date"=> $value['booking']['book_date'],
                "ahass_code"=> $value['booking']['workshop']['code'],
                "ahass_name"=> $value['booking']['workshop']['name'],
                "ahass_address"=> $results ? $results['address'] : '',
                "ahass_contact"=> $results ? $results['phone_number'] : '',
                "ahass_distance"=> $results ? $results['distance'] : 0,
                "motorcycle_ut_code"=> $value['booking']['motorcycle']['ut_code'],
                "motorcycle"=> $value['booking']['motorcycle']['name'],
            ];

            array_push($loop, $data);
            
        }

        if ($request->orderby == 'desc') {
            usort($loop, function($a, $b) {
                return $a['ahass_distance'] <=> $b['ahass_distance'];
            });
        }

        $dataJson = [
            "status" => 1,
            "message" => "Data Successfuly Retrieved.",
            "data" => $loop
        ];

        return response($dataJson, 200);
    }
}
