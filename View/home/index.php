<!-- <a href="/<?=$_ENV["BASENAME"]?>/home/logout" id="logout" class="t-small" style="color: rgb(3, 0, 194);position:absolute; margin:5px;"><i class="fas fa-sign-out-alt"></i></a> -->
<div class="fixed-top">
    <ul class="nav pull-left">
        <li class="dropdown drop"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fas fa-user"></i><b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li><a href="/<?=$_ENV["BASENAME"]?>/home/reset_pass" class="drop"><i class="fas fa-exchange-alt"></i> Password</a></li>
                <li class="divider"></li>
                <li><a href="/<?=$_ENV["BASENAME"]?>/home/logout" class="drop" id="logout"><i class="fas fa-power-off"></i> Logout</a></li>
            </ul>
        </li>
    </ul>
</div>

<div id="desktop">
    <?php if($isAdmin) { ?> <a title="Admin console" href="/<?=$_ENV["BASENAME"]?>/admin/index" class="app t-big" style="color: rgb(3, 0, 194);"><i class="fas fa-user-cog"></i></a> <?php }?>
    <a title="File Browser" href="/<?=$_ENV["BASENAME"]?>/filesystem/browse" class="app t-big" style="color: rgb(253, 214, 107);"><i class="far fa-folder-open"></i></a>
    <?php foreach($icons as $icon)
    { 
        switch($icon["icon_type"])
        {
            case "element":?>
                <a title="<?=$icon["displayname"]?>" href="/<?=$_ENV["BASENAME"]?>/<?=$icon["name"]?>/<?=$icon["start_page"]?>" class="app t-big"><i class="<?=$icon["icon_content"]?>"></i></a>     
            <?php break;
        }
    } ?>
</div>