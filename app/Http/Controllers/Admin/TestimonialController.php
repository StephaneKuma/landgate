<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Testimonial;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $testimonials = Testimonial::latest()->get();

        return view('admin.testimonials.index', compact('testimonials'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.testimonials.create');
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
            'name' => 'required',
            'image' => 'required|mimes:jpeg,jpg,png',
            'testimonial' => 'required|max:200'
        ]);

        $image = $request->file('image');
        $slug  = Str::slug($request->name);

        if(isset($image)){
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

            if(!Storage::disk('public')->exists('testimonial')){
                Storage::disk('public')->makeDirectory('testimonial');
            }
            $testimonial = Image::make($image)->resize(160, 160)->save();
            Storage::disk('public')->put('testimonial/'.$imagename, $testimonial);
        }else{
            $imagename = 'default.png';
        }

        $testimonial = new Testimonial();
        $testimonial->name = $request->name;
        $testimonial->testimonial = $request->testimonial;
        $testimonial->image = $imagename;
        $testimonial->save();

        Toastr::success('message', 'Testimonial created successfully.');
        return redirect()->route('admin.testimonials.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $testimonial = Testimonial::find($id);

        return view('admin.testimonials.edit', compact('testimonial'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'mimes:jpeg,jpg,png',
            'testimonial' => 'required|max:200',
        ]);

        $image = $request->file('image'); 
        $slug  = Str::slug($request->title);
        $testimonial = Testimonial::find($id);

        if(isset($image)){
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();
            if(!Storage::disk('public')->exists('testimonial')){
                Storage::disk('public')->makeDirectory('testimonial');
            }
            if(Storage::disk('public')->exists('testimonial/'.$testimonial->image)){
                Storage::disk('public')->delete('testimonial/'.$testimonial->image);
            }
            $testimonialimg = Image::make($image)->resize(160, 160)->save();
            Storage::disk('public')->put('testimonial/'.$imagename, $testimonialimg);
        }else{
            $imagename = $testimonial->image;
        }

        $testimonial->name = $request->name;
        $testimonial->testimonial = $request->testimonial;
        $testimonial->image = $imagename;
        $testimonial->save();

        Toastr::success('message', 'Testimonial updated successfully.');
        return redirect()->route('admin.testimonials.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $testimonial = Testimonial::find($id);

        if(Storage::disk('public')->exists('testimonial/'.$testimonial->image)){
            Storage::disk('public')->delete('testimonial/'.$testimonial->image);
        }

        $testimonial->delete();

        Toastr::success('message', 'Testimonial deleted successfully.');
        return back();
    }
}
