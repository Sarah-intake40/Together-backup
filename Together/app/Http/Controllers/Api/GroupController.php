<?php

namespace App\Http\Controllers\Api;
use App\Group;
use App\User;
use App\Interest;
use App\UserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    //------------------this function to create a new group
    public function create(Request $request){
      $valid = $request->validate([
        'name' => 'required|min:3|max:255',
        'description' => 'required',
        'status' => 'required',
        'level' => 'required',
        'duration' => 'required',
        'max_member_number'=>'required',
        'interest'=>'required',
        'id'=>'required'
    ]);
      $group=Group::where('name',$request->name)->first();
      if($group){
        return ['response'=>'This group title is exist'];
      }
      $admin=User::find($request->id);
      
      //$group=Group::create($request->except('id','other'));
      $group=new Group();
      $group->admin_id = $admin->id;
      $group->name = $request->name;
      $group->description = $request->description;
      $group->max_member_number = $request->max_member_number;
      $group->duration = $request->duration;
      $group->current_number_of_members = 1;
      $group->level = $request->level;
      $group->status = $request->status;
      $group->photo=$request->photo;
      $interest=Interest::where('name',$request->interest)->first();
      $group->interest_id = $interest->id ;
      $group->save();
      $group->users()->attach($admin);
      return ['response'=>'Group Created Successfully'];
      }
      //-------------------------this fuction to add member to p
      public static function addMember($groupid,$id,Request $request){
       
        $adminMember=User::find($request->input('current_user_id'));
       
        $group=Group::find($groupid);
      
        if($group){
          if($group->admin_id == $adminMember->id){
            
        $user=User::find($id);
        //------------ this user not in the group ??????
        $existUsers=$group->users;
        foreach($existUsers as $exist){
              if($user->id==$exist->id){
                return ['response'=>'This user already in this group'];
              }
        }
        
        if($group->current_number_of_members<$group->max_member_number){
        $group->current_number_of_members=$group->current_number_of_members+1;
        $group->save();
        $group->users()->attach($user);
        return ['response'=>'Member added successfully'];
        }
        else{
          return ['response'=>'This group id full'];
        }
      }
      else{
        return ['response'=>'U aren\'t the admin'];
      }
      return ['response'=>'This group doesnt exist'];
      }
    
    
  }
      //--------------------this function to get this group info
      public function show($groupid){
        
        $group=Group::find($groupid);
        $members=$group->users;
        if($group){
        return ['name'=>$group->name,
        'description'=>$group->sdescription,
        'status'=>$group->status,
        'duration'=>$group->duration,
        'members'=>$members,
        'interest'=>$group->interest->name,
        'photo'=>$group->photo];
        }
        else{
          return ['response'=>'this group id not exist'];
        }
      }
      //---------------------this function to remove member from group
      public function removeMember($groupid,$id,Request $request){
        $adminMember=User::find($request->input('current_user_id'));
        $group=Group::find($groupid);
        if($group){
          if($group->admin_id==$adminMember->id){
        $user=User::find($id);
        $group->users()->detach($user);
        $group->current_number_of_members=$group->current_number_of_members-1;
        if($group->current_number_of_members < 0){
          $group->current_number_of_members=0; 
        }
        $group->save();
        return ['response'=>'member removed successfully'];
        }
      }
      else{
        return ['response'=>'u aren\'t the admin'];
      }
        return ['response'=>'this group doesnt exist'];
      }
      //------------------ this function for user how wants to leave how 7orr
      public function leave($groupid,$id){
        $group=Group::find($groupid);
        $user=User::find($id);
        $group->users()->detach($user);
        $group->current_number_of_members=$group->current_number_of_members-1;
        $group->save();
        return ['response'=>'member leaved successfully'];
      }
      //---------------------------- this function to update group information
      public function updateGroup(Request $request,$groupId){
        $adminMember=User::find($request->input('current_user_id'));
          $group=Group::find($groupId);
          if($group->admin_id == $adminMember->id){
          $group->update($request->only('name','description','address','photo','duration'));
          if($group){
            return ['response'=>'updated successfully'];
          }
        }
        else{
          return ['response'=>'u aren\'t the admin'];
        }
          return ['response'=>'updated failed param error'];
      }
      //-------------------- this to get all request of certain group
      public function requests($groupId){
        $group=Group::find($groupId);
        return $group->requests;
      }
      //--------------------- this for user to send join request
      public function requestToJoin(Request $outRequest,$groupId,$id){
        $request = new UserRequest;
        $request->user_id = $id;
        $request->group_id = $groupId;
        if($outRequest->content){
        $request->request_content = $outRequest->content;
        }
        $request->save();
        return ['response'=>'Request sent successfully wait for admin to accept it'];
    }
    
      
}
