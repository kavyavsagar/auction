<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use DB;
use Illuminate\Support\Arr;
use Validator,Response;
use Carbon\Carbon;

class UserController extends Controller
{
    //

    public function edit($id)
    {
        //
        $user = User::find($id);

        return view('users.edit',compact('user'));
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
        //
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password'
        ]);


        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = bcrypt($input['password']); //Hash::make($input['password']);
        }else{
            $input =  Arr::except($input, ['password']); 
        }
        
        $user = User::find($id);
        $user->update($input);

        return redirect()->route('users.edit')
                        ->with('success','User updated successfully');
    }


}
