<?php

class TreeNode {

    public $text = "";
    public $id = "";
    public $iconCls = "";
    public $leaf = true;
    public $draggable = false;
    public $href = "#";
    public $hrefTarget = "";

    function  __construct($id,$text,$iconCls,$leaf,$draggable,$href,$hrefTarget) {

        $this->id = $id;
        $this->text = $text;
        $this->iconCls = $iconCls;
        $this->leaf = $leaf;
        $this->draggable = $draggable;
        $this->href = $href;
        $this->hrefTarget = $hrefTarget;
    }

    function toJson() {
      return G::json_encode($this);
    }
}

class ExtJsTreeNode extends TreeNode {
    public $children = array();
    function add($object) {
        $this->children[] = $object;
    }

    function toJson() {
        return G::json_encode($this);
    }
}

G::LoadClass('case');

$o = new Cases();

$PRO_UID = '';

$treeArray = array();
//if (isset($_GET['action'])&&$_GET['action']=='test'){
  echo "[";
  // dynaforms assemble
  $extTreeDynaforms = new ExtJsTreeNode("node-dynaforms", G::loadtranslation('ID_DYNAFORMS'), "", false, false, "", "");
  $i = 0;
  $APP_UID = $_GET['APP_UID'];
  $DEL_INDEX = $_GET['DEL_INDEX'];
  $steps = $o->getAllDynaformsStepsToRevise($_GET['APP_UID']);
    foreach ($steps as $step) {
      require_once 'classes/model/Dynaform.php';
      $od = new Dynaform();
      $dynaformF = $od->Load($step['STEP_UID_OBJ']);

      $n = $step['STEP_POSITION'];
      $TITLE   = " - ".$dynaformF['DYN_TITLE'];
      $DYN_UID = $dynaformF['DYN_UID'];
      $PRO_UID = $step['PRO_UID'];
      $href = "cases_StepToRevise?type=DYNAFORM&ex=$i&PRO_UID=$PRO_UID&DYN_UID=$DYN_UID&APP_UID=$APP_UID&position=".$step['STEP_POSITION']."&DEL_INDEX=$DEL_INDEX";
      $extTreeDynaforms->add(new TreeNode($DYN_UID,$TITLE,"datasource",true,false,$href,"openCaseFrame"));
      $i++;
    }
  echo $extTreeDynaforms->toJson();
  // end the dynaforms tree menu
  echo ",";
  // assembling the input documents tree menu
  $extTreeInputDocs = new ExtJsTreeNode("node-input-documents", G::loadtranslation('ID_REQUEST_DOCUMENTS'), "", false, false, "", "");
  $i = 0;
  $APP_UID = $_GET['APP_UID'];
  $DEL_INDEX = $_GET['DEL_INDEX'];
  $steps = $o->getAllInputsStepsToRevise($_GET['APP_UID']);
  //$i=1;
  foreach ($steps as $step) {
    require_once 'classes/model/InputDocument.php';
    $od = new InputDocument();
    $IDF = $od->Load($step['STEP_UID_OBJ']);

    $n = $step['STEP_POSITION'];
    $TITLE = " - ".$IDF['INP_DOC_TITLE'];
    $INP_DOC_UID = $IDF['INP_DOC_UID'];
    $PRO_UID = $step['PRO_UID'];
    $href = "cases_StepToReviseInputs?type=INPUT_DOCUMENT&ex=$i&PRO_UID=$PRO_UID&INP_DOC_UID=$INP_DOC_UID&APP_UID=$APP_UID&position=".$step['STEP_POSITION']."&DEL_INDEX=$DEL_INDEX";
    $extTreeInputDocs->add(new TreeNode($INP_DOC_UID,$TITLE,"datasource",true,false,$href,"openCaseFrame"));
    $i++;
  }
  echo $extTreeInputDocs->toJson();
  // end of the tree assembling input documents list
  echo ",";
  $i=0;
  $APP_UID    = $_GET['APP_UID'];
  $DEL_INDEX  = $_GET['DEL_INDEX'];
  $outputHref = "cases_StepToReviseOutputs?ex=$i&PRO_UID=$PRO_UID&DEL_INDEX=$DEL_INDEX&APP_UID=$APP_UID";
  $ouputItem  = new TreeNode ("node-output-documents",G::loadtranslation('ID_OUTPUT_DOCUMENTS'),"",true,false,$outputHref,"openCaseFrame");
  echo $ouputItem->toJson();
  echo "]";


