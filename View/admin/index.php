<script>
function ShowField(element) {
  if (element.type === "password") {
    element.type = "text";
  } else {
    element.type = "password";
  }
}
</script>

<ul class="nav nav-pills">
      <li>
          <a href="/<?=ROOT_DIR?>/" style="font-size: 1.5em;"><i class="fas fa-chevron-circle-left"></i></a>
      </li>
</ul>

<div class="container">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" data-toggle="tab" href="#users">Users</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#api">API</a>
    </li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">


    <div id="api" class="container tab-pane fade"><br>
      <h3>API Settings</h3>
      <form action="/settings/api" method="POST">
        <div class="form-group">
          <label for="apikey">API Key</label>
          <br>
          <input type="password" name="apikey" id="apikey" class="form-control input-lg" placeholder="Password" tabindex="100" />
          <br>
          <a class="btn btn-primary" onclick="ShowField(document.getElementById('apikey'))">Show ApiKey</a>  
        </div>
        <br>
        <br>
        <div class="form-check">
          <label for="logCheck">Log api actions</label>
          <input type="checkbox" class="form-check-input" id="logCheck">
        </div>
        <br>
        <br>
        <button class="btn btn-primary" type="submit">Save</button>
      </form>
    </div>


    <div id="users" class="container tab-pane active"><br>
      <div class="container mb-2 mt-5">
        <button class="btn btn-primary" data-toggle="modal" data-target="#userModal" data-backdrop="static" data-keyboard="false">New User</button>
      </div>
      <div class="row">
		                <div class="col-md-12">
		                    <table class="table table-hover ">
                                <thead class="bg-light ">
                                  <tr>
                                    <th>
                                      <div class="form-check-inline">
                                          <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" value="">
                                           </label>
                                       </div>
                                    </th>  
                                    <th>Username</th>
                                    <th>Permisions</th>
                                    <th>Edit</th>
                                  </tr>
                                </thead>
                                <tbody>
                                <?php foreach($users as $user) { ?>
                                  <tr>
                                    <td>
                                        <div class="form-check-inline">
                                          <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" value="">
                                           </label>
                                       </div>
                                    </td>  
                                    <td><a href="#"><small><?=$user['name']?></small></a></td>
                                    <td><small>Admin</small></td>
                                    <td>
                                        <a onclick="if(confirm('Are you sure you want to delete this user?')) location.replace('/<?=ROOT_DIR?>/admin/delete_user?id=<?=$user['userID']?>');" href="#"><i class="fa fa-trash"></i></a>
                                        <a href="" data-toggle="modal" data-target="#modal-<?=$user['userID']?>" data-backdrop="static" data-keyboard="false"><i class="fas fa-edit"></i></a>
                                    </td>
                                  </tr>
                                  <div id="modal-<?=$user['userID']?>" class="modal fade" role="dialog">
                                      <div class="modal-dialog modal-lg">
                                          <div class="modal-content">
                                          <form action="/<?=ROOT_DIR?>/admin/update_perms" method="POST">
                                              <div class="modal-body">
                                                  <h3>Set Permisions</h3>
                                                  <div class="form-check">
                                                    <label>Create</label>
                                                    <input class="form-check-input" type="checkbox" name="create" <?=isset($perms[$user['userID']]) && bindec(1000) == (bindec($perms[$user['userID']]) & bindec(1000)) ? "checked" : ""?>>
                                                    <span class="Error"></span>
                                                  </div>
                                                  <div class="form-check">
                                                    <label>Read</label>
                                                    <input class="form-check-input" type="checkbox" name="read" <?=isset($perms[$user['userID']]) && bindec(0100) == (bindec($perms[$user['userID']]) & bindec(0100)) ? "checked" : ""?>>
                                                    <span class="Error"></span>
                                                  </div>
                                                  <div class="form-check">
                                                    <label>Update</label>
                                                    <input class="form-check-input" type="checkbox" name="update" <?=isset($perms[$user['userID']]) && bindec(0010) == (bindec($perms[$user['userID']]) & bindec(0010)) ? "checked" : ""?>>
                                                    <span class="Error"></span>
                                                  </div>
                                                  <div class="form-check">
                                                    <label>Delete</label>
                                                    <input class="form-check-input" type="checkbox" name="delete" <?=isset($perms[$user['userID']]) && bindec(0001) == (bindec($perms[$user['userID']]) & bindec(0001)) ? "checked" : ""?>>
                                                    <span class="Error"></span>
                                                  </div>
                                                  <input class="form-control" type="hidden" name="uid" value="<?=$user['userID']?>">
                                              </div>
                                              <div class="modal-footer">
                                                  <input class="btn btn-primary btn-block" type="submit" value="Submit"/>
                                                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                              </div>
                                          </form>
                                          </div>
                                      </div>
                                  </div>
                                <?php }?>                                       
                                </tbody>
                              </table>
		                </div>
		            </div>
    </div>
  </div>
</div>

<!-- Popups -->
<div id="userModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <form action="/<?=ROOT_DIR?>/admin/create_user" method="post">
            <div class="modal-body">
            <p class="display-2 text-center">Create user</p>
                  <div class="form-group">
                      <label>Username:</label>
                      <input class="form-control" type="text" name="username" required placeholder="Enter Username"/>
                      <span class="Error"></span>
                  </div>
                  <div class="form-group">
                      <label>Password:</label>
                      <input class="form-control" type="password" name="password" required placeholder="Enter Temp Password"/>
                      <span class="Error"></span>
                  </div>

            </div>
            <div class="modal-footer">
                <div class="form-group">
                      <input class="btn btn-primary btn-block" type="submit" value="Create"/>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>