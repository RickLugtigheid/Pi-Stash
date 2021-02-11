<!-- Background -->
<style>
@import url('https://fonts.googleapis.com/css?family=Work+Sans:100&display=swap');

body {
    display: grid;
    height: 100vh;
    margin: 0;
    place-items: center;
    font-family: 'Work Sans', sans-serif;
}
html, body, svg {
  background-color: gray;
  background-image: 
    repeating-linear-gradient(
      45deg,
      rgba(0,0,0,0.8),
      rgba(0,0,0,0.8) 100px,
      transparent 0px,
      transparent 200px
    ),
    repeating-linear-gradient(
      -45deg,
      rgba(0,0,0,0.5),
      rgba(0,0,0,0.5) 100px,
      transparent 0px,
      transparent 200px
    );
}
.app{
  padding: 15px;
}

.t-big{
    font-size: 5em;
}
.t-med{
    font-size: 3em;
}
.t-small{
    font-size: 1.5em;
}
@media (max-width: 700px){
    .t-big{
        font-size: 3em;
    }
    .t-med{
        font-size: 2em;
    }
    .t-small{
        font-size: 1em;
    }
}
</style>

<a href="/<?=$_ENV["BASENAME"]?>/home/logout" id="logout" class="t-small" style="color: rgb(3, 0, 194);position:absolute; margin:5px;"><i class="fas fa-sign-out-alt"></i></a>
<div id="desktop">
    <?php if($_SESSION["perms"] >= 4) { ?> <a title="Admin console" href="/<?=$_ENV["BASENAME"]?>/admin/config" class="app t-big" style="color: rgb(3, 0, 194);"><i class="fas fa-user-cog"></i></a> <?php }?>
    <a title="File Browser" href="/<?=$_ENV["BASENAME"]?>/filesystem/browse" class="app t-big" style="color: rgb(253, 214, 107);"><i class="far fa-folder-open"></i></a>
    <?php //foreach($apps as $app){ ?>
        <!-- <a title="<?=$app["name"]?>" href="/<?=$_ENV["BASENAME"]?>/<?=$app["path"]?>/index" class="app t-big" style="color: rgb(<?=$app["icon-color"]?>);"><i class="<?=$app["icon"]?>"></i></a> -->
    <?php //} ?>
</div>