<style>
.listrap {
        list-style-type: none;
        margin: 0;
        padding: 0;
        cursor: default;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .listrap li {
        margin: 0;
        padding: 10px;
    }

    .listrap li.active, .listrap li:hover {
        background-color: #d9edf7;
    }

    .listrap strong {
        margin-left: 10px;
    }

    .listrap .listrap-toggle {
        display: inline-block;
        width: 60px;
        height: 60px;
    }

    .listrap .listrap-toggle span {
        background-color: #428bca;
        opacity: 0.8;
        z-index: 100;
        width: 60px;
        height: 60px;
        display: none;
        position: absolute;
        border-radius: 50%;
        text-align: center;
        line-height: 60px;
        vertical-align: middle;
        color: #ffffff;
    }

    .listrap .listrap-toggle span:before {
        font-family: 'Glyphicons Halflings';
        content: "\e013";
    }

    .listrap li.active .listrap-toggle span {
        display: block;
    }
</style>

<div class="container">
	<div class="row">
        <ul class="listrap" style="width: 100%;">
        <?php if(CONFIG['guest_account']) {?>
            <li>
                <a href="/<?=ROOT_DIR?>/home/login?path=<?=$path?>&guest=true" title="Login as Guest" class="app t-big" style="color: rgb(3, 0, 194);">
                    <div class="listrap-toggle">
                        <span></span>
                        <i class="img-circle fas fa-user"></i>
                    </div>
                    <strong>Guest</strong>
                </a>
            </li>
            <?php } foreach($users as $user) { ?>
                <li>
                    <a title="Login as <?=$user["name"]?>" class="app t-big" style="color: rgb(3, 0, 194);" data-toggle="modal" data-target="#login-<?=$user["userID"]?>">
                        <div class="listrap-toggle">
                            <span></span>
                            <i class="img-circle fas fa-user"></i>
                        </div>
                        <strong><?=$user["name"]?></strong>
                    </a>
                </li>
                <div class="modal fade login-<?=$user["userID"]?> container mb-2 mt-5" id="login-<?=$user["userID"]?>">
                    <p class="display-2 text-center">Login</p>
                    <a href="" class="t-med"><i class="far fa-times-circle"></i></a>
                    <form action="/<?=ROOT_DIR?>/home/login?path=<?=$path?>" method="post">
                        <div class="form-group">
                            <label>Password:</label>
                            <input class="form-control" type="password" name="password" required placeholder="Enter Password"/>
                            <span class="Error"></span>
                        </div>
                        <input type="hidden" value="<?=$user["userID"]?>" name="id">
                        <input type="hidden" value="<?=$user["name"]?>" name="user">
                        <div class="form-group">
                            <input class="btn btn-primary btn-block" type="submit" value="Submit"/>
                        </div>
                    </form>
                </div>
            <?php }?>
        </ul>
	</div>
</div>