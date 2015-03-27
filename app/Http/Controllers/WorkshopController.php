<?php namespace App\Http\Controllers;

use App\Dfparser;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Src\Factory;
use App\Src\sf;
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


//
//        $data = '<p>'.implode($data,'</p><p>').'</p>';
        $items = Dfparser::all(['item','json'])->toArray();

        foreach ($items as $item)
            $data[$item['item']] = json_decode($item['json']);

        // !!!TEMPORARY!!!
        foreach ($data as &$item)
            foreach ($item as &$value)
                if (is_array($value))
                    $value = sf::mergeWithPattern($value);

        return view('dfparser/workshop/index', compact('data'));
    }


    public function populate()
    {

        $factory = new Factory();
        $factory->loadFilesWithToken('building');

        $data = [];

        foreach ($factory->files as &$file)
            foreach ($file->items as &$item)
            {
                if( ! isset($item->name['string']))
                    continue;

                $item->process();

                $itemDfparser = [
                    'file' => $file->path,
                    'item' => $item->name['array'][0][0][1],
                    'json' => json_encode($item->toArray()),
                ];

                Dfparser::create($itemDfparser);
            }

        dd($factory);
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
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $path = 'building_test';
        $full_path = '/home/vagrant/Code/dfparser/tests/real/' . $path . '.txt';
        $files = Dfparser::find(['file' => $full_path]);

        if($files->isEmpty())
        {
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}
