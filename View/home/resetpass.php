<div class="container mb-2 mt-5">
    <p class="display-2 text-center">Reset Password</p>
    <form action="/<?=$_ENV["BASENAME"]?>/home/update_pass" method="post">
        <div class="form-group">
            <label>Old Password:</label>
            <input class="form-control" type="password" name="password_old" required placeholder="Enter Old Password"/>
            <span class="Error"></span>
        </div>
        <div class="form-group">
            <label>New Password:</label>
            <input class="form-control" type="password" name="password_new" required placeholder="Enter New Password"/>
            <span class="Error"></span>
        </div>
        <div class="form-group">
            <input class="btn btn-primary btn-block" type="submit" value="Submit"/>
        </div>
    </form>
</div>