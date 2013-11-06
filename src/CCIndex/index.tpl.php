<!-- A view showing the reflection class present controller classes and methods. -->
<h1>Index Class</h1>
Shows controller classes and thier public methods.


    <p>This is what you can do for now.</p>
    <br/>
    <?php foreach($menu as $val): ?>
        
    <li><a href='<?=create_url($val)?>'><?=$val?></a> 
    <?php endforeach; ?>   
