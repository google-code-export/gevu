/**
 * ActionScript source file that defines the UI logic and some of the data access code.
 * This file is linked into the main application MXML file using the mx:Script tag.
 * Most of the functions in this file are called by event handlers defined in
 * the MXML.
 */
import compo.*;

import mx.collections.ArrayCollection;
import mx.controls.Alert;
import mx.controls.dataGridClasses.DataGridColumn;
import mx.controls.listClasses.IDropInListItemRenderer;
import mx.events.CloseEvent;
import mx.events.DataGridEvent;
import mx.managers.CursorManager;
import mx.rpc.AsyncToken;
import mx.rpc.events.FaultEvent;
import mx.rpc.events.ResultEvent;
import mx.rpc.http.HTTPService;
import mx.managers.PopUpManager;

//include the constant definition of the server endpoint URL
include "grillesconfig.as";

/**
 * idGrille : identifiant de la grille en cours de consultation
 */
private var idGrille:String = "70";
private var idMot:String = "";
private var idExi:String = "";

/**
 * gateway : this is the communication layer with the server side php code
 */
private var gateway:HTTPService = new HTTPService();

/**
 * the array collection holds the rows that we use in the grid
 */
[Bindable]
public var dataArr:ArrayCollection = new ArrayCollection();
[Bindable]
public var exi:Object;

/**
 * column that we order by. This is updated each time the users clicks on the 
 * grid column header. 
 * see headerRelease="setOrder(event);" in the DataGrid instantiation in the 
 * mxml file
 */
private var orderColumn:Number;


/**
 * the list of fields in the database table
 * needed to parse the response into hashes
 */ 
private var fields:Object = { 'id_donnee':Number, 'ligne_1':String, 'ligne_2':String, 'ligne_3':String, 'ligne_4':String, 'ligne_5':String
	, 'mot_1_titre':String, 'mot_1':Number
	, 'multiple_1_1':Boolean, 'multiple_1_2':Boolean
	, 'multiple_2_1':Boolean, 'multiple_2_2':Boolean, 'multiple_2_3':Boolean, 'multiple_2_4':Boolean, 'multiple_2_5':Boolean, 'multiple_2_6':Boolean
	, 'multiple_3_1':Boolean, 'multiple_3_2':Boolean, 'multiple_3_3':Boolean, 'multiple_3_4':Boolean
	, 'texte_1':String, 'texte_2':String};

private var fieldsSolus:Object = { 'id_solution':Number, 'lib':String, 'id_type_solution':String};
  
 
public function init():void
{
	//construction de la fenêtre d'édition
	var twLog:twLogin= twLogin(
        PopUpManager.createPopUp(this, twLogin, true));
	twLog.endPoint=ENDPOINT_SERVICE;
    PopUpManager.centerPopUp(twLog);
        	
} 
 
public function faultHandlerService(fault:FaultEvent):void
{
Alert.show(fault.fault.faultCode.toString(), "FaultHandlerService");
}
 
public function resultHandlerService(result:ResultEvent):void
{
Alert.show(result.result.toString(), "ResultHandlerService");
}         
            
// Handle the itemEditBegin event.
private function modifyEditedData(event:DataGridEvent):void
{
    // Get the name of the column being editted.
    var colName:String = dataGrid.columns[event.columnIndex].dataField;

    if(colName=="Price")
    {
        // Handle the event here.
        event.preventDefault();

        // Creates an item editor.                
        dataGrid.createItemEditor(event.columnIndex,event.rowIndex);
        
        // All item editors must implement the IDropInListItemRenderer interface
        // and the listData property. 
        // Initialize the listData property of the editor. 
        IDropInListItemRenderer(dataGrid.itemEditorInstance).listData =
            IDropInListItemRenderer(dataGrid.editedItemRenderer).listData;
        
        // Copy the cell value to the NumericStepper control.
        dataGrid.itemEditorInstance.data = dataGrid.editedItemRenderer.data;

   }

	//redimensionne les colonnes text
	/*
    if(colName=="texte_1Col"){
    	dataGrid.rowHeight=100;
   	    var col:DataGridColumn = dataGrid.columns[event.columnIndex];
		col.width = 400;
    }
    */


}



/**
 * Executes when the mxml is completed loaded. Initialize the Rest Gateway.
 */
public function initApp(data:Object):void 
{
	idMot = data.id;
	myservice.hello("tutu");

    /**
     * initialize the gateway
     * - this will take care off server communication and simple xml protocol.
     */
    gateway.url = ENDPOINT_URL;
    gateway.method = "POST";
    gateway.useProxy = false;
    gateway.resultFormat = "e4x";

    /**
     * set the event handler which prevents editing of the primary key
     */
    dataGrid.addEventListener(DataGridEvent.ITEM_EDIT_BEGINNING, editCellHandler);

    /**
     * set the event handler which triggers the update actions - everytime an 
     * edit operation is finished
     */
    dataGrid.addEventListener(DataGridEvent.ITEM_EDIT_END, editCellEnd);

    gateway.addEventListener(ResultEvent.RESULT, resultHandler);
    gateway.addEventListener(FaultEvent.FAULT, faultHandler);
    
    fill();
}


/**
 * Disallow editing of the primary key column.
 * @param e DataGridEvent contains details about the row and column of the grid 
 * where the user clicked
 */
private function editCellHandler(e:DataGridEvent):void
{
    /**
     * if the user clicked on the primary key column, stop editing
     */
    if(e.dataField == "id_donneeCol")
    {
        e.preventDefault();
        return;
    }
}

/**
 * Click handler for "Filter" button.
 * When setting another filter, refresh the collection, and load the new data
 */
private function filterResults():void
{
    fill();
}

/**
 * Event handler triggered when the user finishes editing an entry
 * triggers an "update" server command
 */
private function editCellEnd(e:DataGridEvent):void
{

    var dsRowIndex:int = e.rowIndex;
    var dsFieldName:String = e.dataField;
    var dsColumnIndex:Number = e.columnIndex;

    var vo:* = dataArr[dsRowIndex];
    
    var col:DataGridColumn = dataGrid.columns[dsColumnIndex];
    var newvalue:String = dataGrid.itemEditorInstance[col.editorDataField];

    //récupération de l'identifiant
    var id:String = vo.id_donneeCol;

    trace("a:" + dsRowIndex + ", " + dsFieldName + ", " + dsColumnIndex);
    
    //récupération de l'identifiant de la combo
    if(dsFieldName=="mot_1Col"){
        for each(var c:Object in arrTypeControle) 
        {
        	if(c.label==newvalue)newvalue=c.data;        	
        }    
    }

    var parameters:* =
    {"id_donnee": id, "champ": dsFieldName.substr(0,dsFieldName.length-3), "valeur": newvalue}

	/**
	 * execute the server "update" command
	 */
    doRequest("Update", parameters, saveItemHandler);    
	
}

/**
 * result handler for the "update" server command.
 * Just alert the error, or do nothing if it's ok - the data has already 
 * been updated in the grid
 */
private function saveItemHandler(e:Object):void
{
    if (e.isError)
    {
        Alert.show("Error: " + e.data.error);
    }
    else
    {
    }     
}

/**
 * dragHeader handler for the datagrid. This handler is executed when the user 
 * clicks on a header column in the datagrid
 * updates the global orderColumn variable, refreshes the TableCollection
 * @param event DataGridEvent details about the column
 */
private function setOrder(event:DataGridEvent):void 
{
    orderColumn = event.columnIndex;
    var col:DataGridColumn = dataGrid.columns[orderColumn];
    col.sortDescending = !col.sortDescending;
	ColSelect.text = col.headerText;    
    event.preventDefault();
    fill();
}

/**
 * Click handler for the "Save" button in the "Add" state
 * collects the information in the form and adds a new object to the collection
 */
private function insertItem():void {
    var parameters:* =
    {
		"ligne_1":ligne_1Col.text, "mot_1":mot_1Col.selectedItem.data
		,"ligne_2":ligne_2Col.value,"ligne_3":ligne_3Col.value,"ligne_4":ligne_4Col.value,"ligne_5":ligne_5Col.value
		,"multiple_1_1":multiple_1_1Col.selected,"multiple_1_2":multiple_1_2Col.selected
		,"multiple_2_1":multiple_2_1Col.selected,"multiple_2_2":multiple_2_2Col.selected,"multiple_2_3":multiple_2_3Col.selected,"multiple_2_4":multiple_2_4Col.selected
		,"multiple_2_5":multiple_2_5Col.selected,"multiple_2_6":multiple_2_6Col.selected
		,"multiple_3_1":multiple_3_1Col.selected,"multiple_3_2":multiple_3_2Col.selected,"multiple_3_3":multiple_3_3Col.selected,"multiple_3_4":multiple_3_4Col.selected
		,"texte_1":texte_1Col.text,"texte_2":texte_2Col.text,"id_form":idGrille
	};


	/**
	 * execute the server "insert" command
	 */
    doRequest("Insert", parameters, insertItemHandler);
}

/**
 * Result handler for the insert call.
 * Alert the error if it exists
 * if the call went through ok, return to the list, and refresh the data
 */
private function insertItemHandler(e:Object):void
{
	
    if (e.isError)
    {
        Alert.show("Error: " + e.data.error);
    }else{
    	//pour charger la grille correspondant au mot
    	idMot = e.data.row["mot_1"];
        goToView();
        fill();
    }     
}

/** 
 * general utility function for refreshing the data 
 * gets the filtering and ordering, then dispatches a new server call
 *
 */
private function fill():void 
{
    /**
     * find the order parameters
     */
    var desc:Boolean = false;
    var orderField:String = '';
    
    if(!isNaN(orderColumn))
    {
	    var col:DataGridColumn = dataGrid.columns[orderColumn];
        desc = col.sortDescending;
		//remove the 'Col' particle
	    orderField = col.dataField.substr(0,col.dataField.length-3);
    }


    dataGrid.enabled = false;
    CursorManager.setBusyCursor();

    var parameters:* =
    {
        "orderField": orderField,
        "orderDirection": (desc) ? "DESC" : "ASC", 
        "filter": filterTxt.text,
        "idGrille": idGrille,
        "idMot": idMot,
        "tab": this.tabNavig.selectedChild.id
    }
	/**
	 * execute the server "select" command
	 */
    doRequest("FindAll", parameters, fillHandler);
}

/** 
 * result handler for the fill call. 
 * if it is an error, show it to the user, else refill the arraycollection with the new data
 *
 */
private function fillHandler(e:Object):void
{
    if (e.isError)
    {
        Alert.show("Error: " + e.data.error);
    } 
    else
    {
        dataArr.removeAll();
        for each(var row:XML in e.data.row) 
        {
            var temp:* = {};
            for (var key:String in fields) 
            {
                var current:String = row[key];
                if (current == "true")  
                {
                    temp[key + 'Col'] = true;
                }else if(current == "false"){
                    temp[key + 'Col'] = false;                	
                }else{
	                temp[key + 'Col'] = row[key];            	
                }
            }

            dataArr.addItem(temp);
        }

        CursorManager.removeBusyCursor();
        dataGrid.enabled = true;
    }    
}

/**
 * Click handler for the "delete" button in the list
 * confirms the action and launches the deleteClickHandler function
 */
private function deleteItem():void {
    
    if (dataGrid.selectedItem)
    {
        Alert.show("Etes-vous certain de vouloir supprimer cet élément ?",
        "Confirmation de Suppression", 3, this, deleteClickHandler);
    }
    
}

/**
 * Event handler function for the Confirm dialog raises when the 
 * Delete button is pressed.
 * If the pressed button was Yes, then the product is deleted.
 * @param object event
 * @return nothing
 */ 
private function deleteClickHandler(event:CloseEvent):void
{
    if (event.detail == Alert.YES) 
    {
        var vo:* = dataGrid.selectedItem;

        var parameters:* =
        {
            "id_donnee": vo.id_donneeCol
        }

		/**
		 * execute the server "delete" command
		 */
        doRequest("Delete", parameters, deleteHandler);

        setTimeout( function():void
        {
            dataGrid.destroyItemEditor();
        },
        1);
    }
}

public function deleteHandler(e:*):void
{
    if (e.isError)
    {
        Alert.show("Error: " + e.data.error);
    }
    else
    {
        var id_donnee:Number = parseInt(e.data.toString(), 10);
        for (var index:Number = 0; index < dataArr.length; index++)
        {
            if (dataArr[index].id_donneeCol == id_donnee)
            {
                dataArr.removeItemAt(index);
                break;
            }
        }
    }     
}

/**
 * deserializes the xml response
 * handles error cases
 *
 * @param e ResultEvent the server response and details about the connection
 */
public function deserialize(obj:*, e:*):*
{
    var toret:Object = {};
    
    toret.originalEvent = e;

    if (obj.data.elements("error").length() > 0)
    {
        toret.isError = true;
        toret.data = obj.data;
    }
    else
    {
        toret.isError = false;
        toret.metadata = obj.metadata;
        toret.data = obj.data;
    }

    return toret;
}

/**
 * result handler for the gateway
 * deserializes the result, and then calls the REAL event handler
 * (set when making a request in the doRequest function)
 *
 * @param e ResultEvent the server response and details about the connection
 */
public function resultHandler(e:ResultEvent):void
{
    var topass:* = deserialize(e.result, e);
    e.token.handler.call(null, topass);
}

/**
 * fault handler for this connection
 *
 * @param e FaultEvent the error object
 */
public function faultHandler(e:FaultEvent):void
{
	var errorMessage:String = "Connection error: " + e.fault.faultString; 
    if (e.fault.faultDetail) 
    { 
        errorMessage += "\n\nAdditional detail: " + e.fault.faultDetail; 
    } 
    Alert.show(errorMessage);
}

/**
 * makes a request to the server using the gateway instance
 *
 * @param method_name String the method name used in the server dispathcer
 * @param parameters Object name value pairs for sending in post
 * @param callback Function function to be called when the call completes
 */
public function doRequest(method_name:String, parameters:Object, callback:Function):void
{
    // add the method to the parameters list
    parameters['method'] = method_name;

    gateway.request = parameters;

    var call:AsyncToken = gateway.send();
    call.request_params = gateway.request;
	
    var query:String=gateway.url+"?bug=true";
	for (var i:String in parameters)
	{
	    query= query+"&"+i+"="+parameters[i];
	}
	trace ("doRequest query=" +query);
	
    call.handler = callback;
}


/**
 * Click handler when the user click the "Create" button
 * Load the "Update" canvas.
 */
public function goToUpdate():void
{
	if(this.tabNavig.selectedChild.id=="tabCrit")
		applicationScreens.selectedChild = update;
}

/**
 * Load the "View" canvas.
 */
public function goToView():void
{
	if(this.tabNavig.selectedChild.id=="tabCrit")
	    applicationScreens.selectedChild = view;
}
