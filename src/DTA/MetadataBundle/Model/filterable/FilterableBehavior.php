<?php
/**
 * Created by PhpStorm.
 * User: binder
 * Date: 03.03.15
 * Time: 19:18
 */



class FilterableBehavior extends Behavior {

    // default parameters value
    protected $parameters = array();

    //\DTA\MetadataBundle\Model\SQLFilterable
    public function queryMethods(){
        $filterFunctions = array();
        foreach($this->getParameters() as $key => $value){
        //foreach($this->filterColumns as $filterColumnCaption){
            if($value === 'atomic') {
                //$accessor = ORMController::extractPureAccessor($this->accessors[$filterColumnCaption]);
                //$accessor = $this->accessors[$filterColumnCaption];
                //$filterColumn = $this->getTable()->getColumnByPhpName($accessor);
                $filterColumn = $this->getTable()->getColumn($key);
                $filterType = null;
                $template = "filterableFilterNone";
                if ($filterColumn !== null) {
                    $filterType = $filterColumn->getType();
                    if ($filterColumn->isTextType()) {
                        $template = "filterableFilterText";
                    } elseif ($filterColumn->isNumericType()) {
                        $template = "filterableFilterNumeric";
                    } elseif ($filterColumn->isTemporalType()) {
                        //TODO
                    } else {
                        // use template "tableRowViewFilterMethodNone"
                    }
                }
                $filterFunctions[] = $this->renderTemplate($template, array(
                    'filterElement' => $filterColumn->getPhpName(),
                    'filterType' => $filterType
                ));
            }elseif($value === 'many'){
                //$relatedTable = $this->getTable()->getDatabase()->getTable($key);
                $filterFunctions[] = $this->renderTemplate('filterableFilterEmbedded', array(
                    'filterElement' => $key
                ));
            }elseif($value === 'manyToMany'){
                //$relatedTable = $this->getTable()->getDatabase()->getTable($key);
                $filterFunctions[] = $this->renderTemplate('filterableFilterEmbeddedEmbedded', array(
                    'filterElement' => $key,
                    'thisElement' => $this->getTable()->getPhpName()
                ));
            }else{
                $tableName = $this->getTable()->getName();
                throw new Exception("Unknown value \"$value\" for \"$key\" in filterableBehavior for table \"$tableName\"");
            }
        }


        return $this->renderTemplate( 'filterableSQLFilterMethod',array(
            'className' => $this->getTable()->getPhpName(),
            'filterFunctions' => $filterFunctions
        ));
    }

    // add interface implementation declaration
    /*public function queryFilter(&$script) {
        //$pattern = '/abstract class (\w+) extends (\w+) implements (\w+)/i';
        $pattern = '/abstract class (\w+) extends (\w+) implements (\w+)/i';
        // the comment causes problems if other behaviors want to add interfaces, too. "// TableRowViewInterface automatically implemented by the TableRowViewBehavior.php\r" .
        $replace = 'abstract class ${1} extends ${2} implements ${3}, \DTA\MetadataBundle\Model\filterable\FilterableInterface';
        $script = preg_replace($pattern, $replace, $script);
    }*/
} 