<style>
.debug-layer {
    position:fixed;
    padding:0;
    margin:0;
    top:0;
    left:0;
    width: 325px;
    height: 100%;
    z-index: 100;
    overflow:hidden;
    border:solid 2px grey;
    background-color:#f8f8f8;
}

.debug-layer.minimize {
    top:50%;
    height:25px;
    width:55px;
}

.iframe-container {
    height: 99%;
    width: 100%;
    overflow: scroll;
}

.header-container {
    width:100%;
    height:25px;
    position:relative;
    background:rgba(12, 250, 101, 0.9);
}

.header-container> button.layer-toggle {
    float:right;
    background-color:yellow;
    font-weight:bold;
}

.debug-layer.minimize>.header-container> button.layer-toggle {
    background-color:red;
}

</style>

<div class="debug-layer minimize">
    <div class="header-container">
        <button class="layer-toggle">Debug Theme</button><br>
    </div>
    <div class="iframe-container">
    <iframe height="100%" width="320px" src="/debugiframe"></iframe>
    </div>
</div>

<script>
function classToggle() {
    document.querySelector('.debug-layer').classList.toggle('minimize');
}
document.querySelector('.layer-toggle').addEventListener('click', classToggle);
</script>