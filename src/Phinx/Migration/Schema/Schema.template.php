<?php use Phinx\Migration\Helper\CodeGenerator; ?>
<?php echo "<?php"; ?>

use Phinx\Migration\AbstractMigration;

class Schema extends AbstractMigration
{
    public function change()
    {
    <?php foreach ($tables as $table) : ?>
        $this->table('<?php echo $table->getName();?>', <?php echo CodeGenerator::buildTableOptionsString($table); ?>)
        <?php $columns = $table->getColumns(); ?>
        <?php foreach ($columns as $column) : ?>
            <?php if (!CodeGenerator::isColumnSinglePrimaryKey($table, $column)) : ?>
                ->addColumn(<?php echo CodeGenerator::buildAddColumnArgumentsString($column);?>)
            <?php endif; ?>
        <?php endforeach; ?>
        ->create();

    <?php endforeach; ?>
    <?php foreach ($tables as $table) : ?>
        <?php
        $rows = $table->fetchAll();
        foreach($rows as $index => $row) {
            foreach($row as $key => $value) {
                if(is_numeric($key)) {
                    unset($rows[$index][$key]);
                }
            }
        }
        if(!empty($rows)) : ?>
            $data = <?php echo var_export($rows, true); ?>;
            $this->table('<?php echo $table->getName(); ?>')->insert($data);
        <?php endif; ?>
    <?php endforeach; ?>
    <?php foreach ($tables as $table) : ?>
        <?php
    //    $foreignKeys = $table->getAdapter()->getForeignKeys($table->getName());
        $foreignKeys = $table->getForeignKeys();
        if (count($foreignKeys) > 0) : ?>
            $this->table('<?php echo $table->getName();?>', <?php echo CodeGenerator::buildTableOptionsString($table); ?>)
            <?php foreach ($foreignKeys as $foreignKey) : ?>
                <?php echo CodeGenerator::buildFkString($foreignKey); ?>

            <?php endforeach; ?>
            ->update();

        <?php endif; ?>
    <?php endforeach; ?>
    }
}
