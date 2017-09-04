<div class="center">
    <div class="centralized">
        <h3><?php echo "{$status} - {$title}"; ?></h3>
        <div id="display-error" class="btn btn-default">
            <i class="fa fa-angle-right"></i>
            <?php echo $message; ?>
        </div>
    </div>
    <div class="clear"><br></div>

    <ul class="list-group" id="debug-error" style="display:none">
        <?php foreach($dump as $index => $lines): ?>
            <li class="list-group-item active">
                <label><?php echo "File: ".($index+1); ?></label>
            </li>
            <?php foreach($lines as $key => $line): ?>
                <?php if( !(is_object($line) || is_array($line)) ): ?>
                    <li class="list-group-item">
                        <label><?php echo $key; ?>:</label>
                        <?php echo $line; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </ul>
</div>