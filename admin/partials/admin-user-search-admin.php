<div class="container-fluid"  ng-app="admin_user_app"  ng-controller="admin_user_controller" ng-cloak>
   <div class="row">
        <div class="col-12">

            <div class="card shadow p-3 mb-5 bg-body rounded" style="margin-top:50px; max-width:100%">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4" >
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Search users" aria-label="search user" ng-model="search_keywords" id="search_user_key">
                                <button class="btn btn-labeled btn-info" type="button"  ng-click="search_users_exec()"><i class="gg-search"></i></button>
                            </div>
                        </div>
                        <?php 
?>
                    </div>

                </div>
                <!-- /card-body -->    
                <div class="card-body">
                    <?php 
?>
                                            <?php 
?>
                                        <?php 
?>
                    <hr style="margin:0px; padding:0px">
                    <div class="row">

                        <div class="col-md-12">
                            <br>
                            <h3 class=""><# aus_progress_verbiage #></h3>
                            <div class="progress"  ng-if="aus_search_in_progress == 1">
                                <div class="progress-bar progress-bar-striped bg-info progress-bar-animated" style="width:<# aus_progress_val #>"><# aus_progress_val #></div>
                            </div>
                        </div>

                        <div class="col-md-12"  ng-if="aus_search_in_progress == 0">

                            <div class="table-responsive">
                                <table  datatable="ng" class="table table-hover table-striped " dt-options="dtOptions_users">
                                    <thead>
                                        <tr>
                                            <td  style="white-space: nowrap;">First name</td>
                                            <td  style="white-space: nowrap;">Last name</td>
                                            <td  style="white-space: nowrap;">Email</td>
                                            <td  style="white-space: nowrap;">Roles</td>
                                            <td  style="white-space: nowrap;">Actions</td>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr ng-repeat="user in aus_current_users track by $index" style="cursor:pointer">

                                        <td> <# user.first_name #></td>
                                        <td> <# user.last_name #></td>
                                        <td> <# user.email #></td>
                                        <td>  <span ng-repeat="role in user.roles" class="badge bg-info" style="margin-right:3px"> <# role #> </span> </td>
                                        <td> <a href='/wp-admin/user-edit.php?user_id=<#user.ID#>' data-toggle='tooltip' data-placement='top' title='Edit user informations' target="_blank"> <i class="gg-arrow-top-right-r"></i> </a> </td>
                                        
                                        </tr>

                                    </tbody>
                                </table>
                            </div> <!-- table responsive -->
                        </div><!-- /col-12 -->
                    </div><!-- /row -->
                </div><!-- /card-body -->  


            </div><!-- /card  --> 
         </div><!-- /col-12 -->
   </div><!-- /row -->
</div><!-- container-fluid -->
