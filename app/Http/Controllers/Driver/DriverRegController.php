<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver\DriverReg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DriverRegController extends Controller
{
    public $driver;

    public function __construct()
    {
        $this->driver = new DriverReg();
    }

    //get all drivers
    public function getDriversAll()
    {
        $response = $this->driver->fetchRegisteredDrivers();

        return $response;
    }

    //create a new driver user
    public function createNewDriverUser(Request $request)
    {
        try {

            $full_name = $request['full_name'];
            $contact_number = $request['contact_number'];
            $driver_nic = $request['driver_nic'];
            $vehicle_type = $request['vehicle_type'];
            $vehicle_no = $request['vehicle_no'];
            $other_remarks = $request['other_remarks'];

            $rand_char = Str::random(8);

            $Username = strtok($full_name, ' '); //do not remove the SPACE that given in strtok() function second parameter, it will effect driver username
            $Password = Hash::make($rand_char);

            $prof_image = array();
            $other_docs = array();


            // return [
            //     $full_name,
            //     $contact_number,
            //     $driver_nic,
            //     $vehicle_type,
            //     $vehicle_no,
            //     $other_remarks,
            //     $rand_char,
            //     $Username,
            //     $Password
            // ];

            if ($request->has('prof_image') || $request->has('other_docs')) {

                if ($request->has('prof_image')) {

                    $profile_img = $request->file('prof_image');

                    foreach ($profile_img as $profImage) {
                        $image_name = $profImage->getClientOriginalName();
                        $image_extension = $profImage->getClientOriginalExtension();
                        $upload_path = 'uploads/driverDocs/';
                        $image_url = $upload_path . $image_name;
                        $profImage->move($upload_path, $image_name);
                        $prof_image[] = $image_url;
                    }
                }
                if ($request->has('other_docs')) {

                    $other_documents = $request->file('other_docs');

                    foreach ($other_documents as $otherdoc) {
                        $doc_name = $otherdoc->getClientOriginalName();
                        $doc_extension = $otherdoc->getClientOriginalExtension();
                        $upload_path = 'uploads/driverDocs/';
                        $doc_url = $upload_path . $doc_name;
                        $otherdoc->move($upload_path, $doc_name);
                        $other_docs[] = $doc_url;
                    }
                }
            }

            $response = $this->driver->createNewDriver($full_name, $contact_number, $driver_nic, $vehicle_type, $vehicle_no, $other_remarks, $prof_image, $other_docs, $Username, $Password, $rand_char);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
