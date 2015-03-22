<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class WorkshopController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        // Check for workshops in db


        $factory = new \Asva\DFParser\Factory();
        $factory->loadFilesWithToken('building');
        foreach ($factory->link as $link){
            if (is_a($link, 'Asva\DFParser\Item')
                && isset($link->name['string'])){
                $files = new \App\Dfparser();
                $files->file = $link->file->path;
                $files->item = $link->sObj[1];
                $files->save();
                dd($files->id);
            }

        }

        $data = '<p>'.implode($data,'</p><p>').'</p>';

        return view('dfparser/workshop',compact('data'));

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        $path = 'building_test';
        $full_path = '/home/vagrant/Code/dfparser/tests/real/'.$path.'.txt';
        $files = \App\Dfparser::find(['file' => $full_path]);

        if ($files->isEmpty()) {}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
