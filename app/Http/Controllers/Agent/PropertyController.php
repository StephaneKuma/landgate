<?php

namespace App\Http\Controllers\Agent;

use Carbon\Carbon;
use App\Models\Feature;
use App\Models\Property;
use Illuminate\Http\Request;
use App\Models\PropertyImage;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $properties = Property::latest()
            ->withCount('comments')
            ->where('agent_id', Auth::id())
            ->paginate(10);

        return view('agent.properties.index',compact('properties'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $features = Feature::all();

        return view('agent.properties.create', compact('features'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|unique:properties|max:255',
            'price'     => 'required',
            'purpose'   => 'required',
            'type'      => 'required',
            'bedroom'   => 'required',
            'bathroom'  => 'required',
            'city'      => 'required',
            'address'   => 'required',
            'area'      => 'required',
            'image'     => 'required|image|mimes:jpeg,jpg,png',
            'floor_plan'=> 'image|mimes:jpeg,jpg,png',
            'description'        => 'required',
            'location_latitude'  => 'required',
            'location_longitude' => 'required',
        ]);

        $image = $request->file('image');
        $slug  = str_slug($request->title);

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('property')) {
                Storage::disk('public')->makeDirectory('property');
            }

            $propertyimage = Image::make($image)->stream();
            Storage::disk('public')->put('property/'.$imagename, $propertyimage);
        }

        $floor_plan = $request->file('floor_plan');

        if (isset($floor_plan)) {
            $currentDate = Carbon::now()->toDateString();
            $imagefloorplan = 'floor-plan-'.$currentDate.'-'.uniqid().'.'.$floor_plan->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('property')) {
                Storage::disk('public')->makeDirectory('property');
            }
            $propertyfloorplan = Image::make($floor_plan)->stream();
            Storage::disk('public')->put('property/'.$imagefloorplan, $propertyfloorplan);
        } else {
            $imagefloorplan = 'default.png';
        }

        $property = new Property();
        $property->title    = $request->title;
        $property->slug     = $slug;
        $property->price    = $request->price;
        $property->purpose  = $request->purpose;
        $property->type     = $request->type;
        $property->image    = $imagename;
        $property->bedroom  = $request->bedroom;
        $property->bathroom = $request->bathroom;
        $property->city     = $request->city;
        $property->city_slug= str_slug($request->city);
        $property->address  = $request->address;
        $property->area     = $request->area;

        if (isset($request->featured)) {
            $property->featured = true;
        }

        $property->agent_id           = Auth::id();
        $property->video              = $request->video;
        $property->floor_plan         = $imagefloorplan;
        $property->description        = $request->description;
        $property->location_latitude  = $request->location_latitude;
        $property->location_longitude = $request->location_longitude;
        $property->nearby             = $request->nearby;
        $property->save();

        $property->features()->attach($request->features);


        $gallary = $request->file('gallaryimage');

        if ($gallary) {
            foreach($gallary as $images) {
                $currentDate = Carbon::now()->toDateString();
                $galimage['name'] = 'gallary-'.$currentDate.'-'.uniqid().'.'.$images->getClientOriginalExtension();
                $galimage['size'] = $images->getClientSize();
                $galimage['property_id'] = $property->id;
                
                if (!Storage::disk('public')->exists('property/gallery')) {
                    Storage::disk('public')->makeDirectory('property/gallery');
                }

                $propertyimage = Image::make($images)->stream();
                Storage::disk('public')->put('property/gallery/'.$galimage['name'], $propertyimage);

                $property->gallery()->create($galimage);
            }
        }

        Toastr::success('message', 'Property created successfully.');
        return redirect()->route('agent.properties.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  Property $property
     * @return \Illuminate\Http\Response
     */
    public function show(Property $property)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Property $property
     * @return \Illuminate\Http\Response
     */
    public function edit(Property $property)
    {
        $features = Feature::all();
        $property = Property::where('slug', $property->slug)->first();

        return view('agent.properties.edit', compact('property','features'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Property $property
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Property $property)
    {
        $request->validate([
            'title'     => 'required|max:255',
            'price'     => 'required',
            'purpose'   => 'required',
            'type'      => 'required',
            'bedroom'   => 'required',
            'bathroom'  => 'required',
            'city'      => 'required',
            'address'   => 'required',
            'area'      => 'required',
            'image'     => 'image|mimes:jpeg,jpg,png',
            'floor_plan'=> 'image|mimes:jpeg,jpg,png',
            'description'        => 'required',
            'location_latitude'  => 'required',
            'location_longitude' => 'required'
        ]);

        $image = $request->file('image');
        $slug  = str_slug($request->title);

        $property = Property::find($property->id);

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('property')) {
                Storage::disk('public')->makeDirectory('property');
            }

            if (Storage::disk('public')->exists('property/'.$property->image)) {
                Storage::disk('public')->delete('property/'.$property->image);
            }

            $propertyimage = Image::make($image)->stream();
            Storage::disk('public')->put('property/'.$imagename, $propertyimage);

        } else {
            $imagename = $property->image;
        }


        $floor_plan = $request->file('floor_plan');

        if (isset($floor_plan)) {
            $currentDate = Carbon::now()->toDateString();
            $imagefloorplan = 'floor-plan-'.$currentDate.'-'.uniqid().'.'.$floor_plan->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('property')) {
                Storage::disk('public')->makeDirectory('property');
            }

            if (Storage::disk('public')->exists('property/'.$property->floor_plan)) {
                Storage::disk('public')->delete('property/'.$property->floor_plan);
            }

            $propertyfloorplan = Image::make($floor_plan)->stream();
            Storage::disk('public')->put('property/'.$imagefloorplan, $propertyfloorplan);

        } else {
            $imagefloorplan = $property->floor_plan;
        }

        $property->title    = $request->title;
        $property->slug     = $slug;
        $property->price    = $request->price;
        $property->purpose  = $request->purpose;
        $property->type     = $request->type;
        $property->image    = $imagename;
        $property->bedroom  = $request->bedroom;
        $property->bathroom = $request->bathroom;
        $property->city     = $request->city;
        $property->city_slug= str_slug($request->city);
        $property->address  = $request->address;
        $property->area     = $request->area;

        if (isset($request->featured)) {
            $property->featured = true;
        } else {
            $property->featured = false;
        }

        $property->description          = $request->description;
        $property->video                = $request->video;
        $property->floor_plan           = $imagefloorplan;
        $property->location_latitude    = $request->location_latitude;
        $property->location_longitude   = $request->location_longitude;
        $property->nearby               = $request->nearby;
        $property->save();

        $property->features()->sync($request->features);

        $gallary = $request->file('gallaryimage'); 
        if ($gallary) {
            foreach ($gallary as $images) {
                if (isset($images)) {
                    $currentDate = Carbon::now()->toDateString();
                    $galimage['name'] = 'gallary-'.$currentDate.'-'.uniqid().'.'.$images->getClientOriginalExtension();
                    $galimage['size'] = $images->getClientSize();
                    $galimage['property_id'] = $property->id;
                    
                    if (!Storage::disk('public')->exists('property/gallery')) {
                        Storage::disk('public')->makeDirectory('property/gallery');
                    }

                    $propertyimage = Image::make($images)->stream();
                    Storage::disk('public')->put('property/gallery/'.$galimage['name'], $propertyimage);

                    $property->gallery()->create($galimage);
                }
            }
        }

        Toastr::success('message', 'Property updated successfully.');
        return redirect()->route('agent.properties.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Property $property
     * @return \Illuminate\Http\Response
     */
    public function destroy(Property $property)
    {
        $property = Property::find($property->id);

        if (Storage::disk('public')->exists('property/'.$property->image)) {
            Storage::disk('public')->delete('property/'.$property->image);
        }

        if (Storage::disk('public')->exists('property/'.$property->floor_plan)) {
            Storage::disk('public')->delete('property/'.$property->floor_plan);
        }

        $property->delete();
        
        $galleries = $property->gallery;

        if ($galleries) {
            foreach ($galleries as $key => $gallery) {
                if(Storage::disk('public')->exists('property/gallery/'.$gallery->name)){
                    Storage::disk('public')->delete('property/gallery/'.$gallery->name);
                }
                PropertyImageGallery::destroy($gallery->id);
            }
        }

        $property->features()->detach();
        
        Toastr::success('message', 'Property deleted successfully.');
        return back();
    }

    public function galleryImageDelete(Request $request)
    {
        $gallaryimg = PropertyImage::find($request->id)->delete();

        if (Storage::disk('public')->exists('property/gallery/'.$request->image)) {
            Storage::disk('public')->delete('property/gallery/'.$request->image);
        }

        if ($request->ajax()) {
            return response()->json(['msg' => true]);
        }
    }
}
