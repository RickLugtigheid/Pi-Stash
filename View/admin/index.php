<script>
function ShowField(element) {
  if (element.type === "password") {
    element.type = "text";
  } else {
    element.type = "password";
  }
}
</script>

<div class="container">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" data-toggle="tab" href="#system">System</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#users">Users</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#api">API</a>
    </li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div id="system" class="container tab-pane active"><br>
      <h3>System Settings</h3>
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
    </div>


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


    <div id="users" class="container tab-pane fade"><br>
      <div class="container mb-2 mt-5">
          <p class="display-2 text-center">Create user</p>
          <form action="/<?=$_ENV["BASENAME"]?>/admin/createUser" method="post">
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
              <div class="form-group">
                  <label>Permisions:</label>
                  <input class="form-control" type="number" min="1" name="perms" required placeholder="Enter Perms; 0001"/>
              </div>
              <div class="form-group">
                  <input class="btn btn-primary btn-block" type="submit" value="Submit"/>
              </div>
          </form>
      </div>
    </div>
  </div>
</div>