<?php

namespace App\Http\Controllers\Api;
use App\User;
use App\Group;
use Laravel\Sanctum\PersonalAccessToken;
use App\Interest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\HasApiTokens;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //-------------------this function to sign up 
    public function signup(Request $request){
        //----------- this to vslidate request
        $valid = $request->validate([
            'name' => 'required|min:3|max:255',
            'email' => 'required',
            'password' => 'required',
            'gender' => 'required',
            'BirthDate' => 'required',
        ]);
        //-----here i ckeck if this is his first account in our app
        if($valid){
        $user=User::where('email',$request->email)->first();
        if($user){
            return ['response'=>'This email is exist '];
        }
        //-------------- then if itis his first sign up
        $user=new User; 
        // -------------------here if pic is attached
        // if($request->file('photo')){
        //     $image=$request->photo;
        //     $destinationPath = 'images/'; // upload path
        //     $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
        //     $image->move($destinationPath, $profileImage);
        //     $user->photo = $profileImage;   
        // }
        // ------------her to attach his intrests
        $interestArr=array();
        if($request->interests){
         $listOfInterests=$request->interests;
          foreach($listOfInterests as $interest){
             array_push($interestArr,Interest::where('name',$interest)->first()->id);
             }
            }
           
        //--------------------here if user has other interests allah y5rb bytooo
        $user->name = $request->name;
        $user->email=$request->email;
        $user->fill(['password' => encrypt($request->password)]);
        $user->BirthDate =$request->BirthDate;
        $user->gender = $request->gender;
        $user->address=$request->address;
        $user->photo=$request->photo;
        $user->save();
        //---------here i attach el inteerests
        $user->interests()->attach($interestArr);
        //------------- here user saved
        
        if($user){
       // return ['response'=>'Signed up Successfully'];
          $response=$this->signin($request);
          return $response;
         }

        else {

            return ['response'=>'Plz fill all required feilds'];
        }
    }
}
    //----------------------this function to login
    public function signin(Request $request){
        $valid = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
         $user=User::where('email',$request->email)->first();
         
         if($user){
            //return $user->email;
           //  $user=User::where('password',$request->password)->first();
           $pasword=Crypt::decrypt($user->password);
             if($request->password == $pasword){
                $token=$user->createToken($request->email)->plainTextToken;
                 return ['token' => $token,'id'=>$user->id];
             }
             else{
                return ['response'=>'Password not correct'];
             }
         }
         else{
            return ['response'=>'This mail not registered'];
         }
    }
    //---------------------------- this function to view Profile
    public function show(Request $request){
        $id=$request->input('id');
        $user=User::where('id',$id)->first();
        //----------here to return interests of user

        $interestsList=$user->interests;
        $userInterests=array();
         foreach($interestsList as $interest){
           array_push($userInterests,$interest->name);
         }
         $groups=$user->groups;
         $groupsNames=array();
         if($groups){
         foreach($groups as $group){
             array_push($groupsNames,['name'=>$group->name,'id'=>$group->id]);
         }
        }
        if($user){
        return [
        'name'=>$user->name,
        'email'=>$user->email,
        'gender'=>$user->gender,
        'password'=>decrypt($user->password),
        'BirthDate'=>$user->BirthDate,
        'address'=>$user->address,
        'interests'=>$userInterests,
        'groups'=>$groupsNames];
        }
        else{
            return ['response'=>'Error param'];
        }
    }
    //------------------------------ this function to edit profile
    public function update(Request $request,$id){
            $user=User::where('id',$id)->first();
            if($user){
            //$user=User::where('id',$id)->first()->update($request->all());
            $user->name = $request->name;
            $user->email=$request->email;
            $user->fill(['password' => encrypt($request->password)]);
            $user->BirthDate =$request->BirthDate;
            $user->gender = $request->gender;
            $user->address=$request->address;
            $user->update();
            return ['response'=>'Updated Successfully'];
    }
       else{
        return ['response'=>'This user is not exist'];               
    }
    }
    //------------------- this to retrive all groups of certain user
    public function home($id){
        $user=User::where('id',$id)->first();
        if($user){
            return $user->groups;
        }
        else{
            return ['response'=>'This user is not exist'];  
        }
    }
    //---------------------------- this function to update interests of user
    public function updateInterests(Request $request,$id){
        $user=User::find($id);
        if($user){
            $interestArr=array();
             $listOfInterests=$request->interests;
              foreach($listOfInterests as $interest){
                 array_push($interestArr,Interest::where('name',$interest)->first()->id);
                 }
                 $user->interests()->sync($interestArr);
                 $user->save(); 
                 return ['response'=>'Interests changed successfully'];
        }
        return ['response'=>'This user is not exist'];  
      }
       //---------------------------- this function used to logout
       public function logout($id){
           $user=User::find($id);
           if($user){
              //Auth::logout();
              $tokens=PersonalAccessToken::where('name',$user->email)->get();
              foreach($tokens as $token){
                $token->delete();
              }
              
              return ['response'=>'logout successfully'];
           }

       }
       //----------------------------- this to return status of user(one of group , not in send request)
       public function getStatus ($groupId,$id){
           $group=Group::find($groupId);
           $members=$group->users;
          // return $members;
           foreach ($members as $member){
               if($member->id==$id){
                   $status ='Member of this group';
               }
               else {
                   $status = 'Not member';
               }
           }
           if($status){
               $case=$status;
               $status=' ';
           }
           $requests=$group->requests;
           foreach($requests as $request){
               if($request->user_id==$id){
                   $status ='This user waiting for admin of group to accept his request of join';
               }
           }
           return ['response'=>$case .' and '.$status];
       }
}
