package it.sephiroth.controls
{
	import flash.events.Event;
	
	import it.sephiroth.controls.treeClasses.TreeCheckListData;
	import it.sephiroth.renderers.TreecheckboxItemRenderer;
	
	import mx.controls.Tree;
	import mx.controls.listClasses.BaseListData;
	import mx.controls.treeClasses.TreeListData;
	import mx.core.ClassFactory;
	import mx.core.mx_internal;
	import mx.events.TreeEvent;
	
	use namespace mx_internal;

	[Event(name="checkFieldChanged", type="flash.events.Event")]
	[Event(name="checkFunctionChanged", type="flash.events.Event")]
	[Event(name="itemCheck", type="mx.events.TreeEvent")]
	
	public class TreeCheckBox extends Tree
	{
		protected var _checkField: String;
		private var _checkFunction: Function;
		
		public function TreeCheckBox()
		{
			super( );
			this.itemRenderer = new ClassFactory( TreecheckboxItemRenderer );
			addEventListener( "itemCheck", checkHandler );
		}
		
	    mx_internal function isBranch( item: Object ): Boolean
	    {
	        if ( item != null )
	            return _dataDescriptor.isBranch( item, iterator.view );
	        return false;
	    }		
		
 		private function checkHandler( event: TreeEvent ): void
	    {
	    	var value: String;
	        var state: int = ( event.itemRenderer as TreecheckboxItemRenderer ).checkBox.checkState;
	        var middle: Boolean = ( state & 2 << 1 ) == ( 2 << 1 );
	        var selected: Boolean = ( state & 1 << 1 ) == ( 1 << 1 );

			if( isBranch( event.item ) )
			{
				middle = false;
			}  
	        
	        if( middle )
	        {
	            value = "2";
	        } else {
	            value = selected ? "1" : "0";
	        }
	        
	        var data:Object = event.item;

	        if (data == null) {
	            return;
	        }
	
	        if( checkField )
	        {
	            if ( data is XML )
	            {
	                try
	                {
	                       data[ checkField ] = value;
	                }
	                catch ( e: Error )
	                {
	                }
	            } else if ( data is Object ) {
	                try
	                {
	                    data[ checkField ] = value;
	                }
	                catch( e: Error )
	                {
	                }
	            }
	         }
	
	        if ( data is String ) {
	            data = String( value );
	        }
	        commitProperties( );
	    }
		
		
	    override protected function makeListData( data: Object, uid: String, rowNum: int): BaseListData
	    {
	        var treeListData: TreeListData = new TreeCheckListData( itemToLabel( data ), uid, this, rowNum );
	        initListData( data, treeListData );
	        return treeListData;
	    }
	    
	    override protected function initListData( item: Object, treeListData: TreeListData ): void
	    {
	    	super.initListData( item, treeListData );
	    	
	        if (item == null)
	            return;
	
	        ( treeListData as TreeCheckListData ).checkedState = itemToCheck( item );
	    }	    
		
    	[Bindable("checkFieldChanged")]
    	[Inspectable(category="Data", defaultValue="checked")]		
	    public function get checkField( ): String
	    {
	        return _checkField;
	    }
	
	    public function set checkField( value: String ): void
	    {
	        _checkField = value;
	        itemsSizeChanged = true;
	        invalidateDisplayList( );
	        dispatchEvent( new Event("checkFieldChanged") );
	    }
	    
	    public function itemToCheck( data: Object ): int
	    {
	        if (data == null )
	            return 0;
	
	        if ( checkFunction != null )
	            return checkFunction( data );
	
	        if ( data is XML )
	        {
	            try
	            {
	                if ( data[ checkField ].length( ) != 0 )
	                    data = data[ checkField ];
	            } catch( e: Error )
	            {
	            }
	        }
	        else if ( data is Object )
	        {
	            try
	            {
	                if ( data[ checkField ] != null )
	                    data = data[ checkField ];
	            } catch( e: Error )
	            {
	            }
	        }
	
	        if ( data is String )
	            return parseInt( String( data ) );
	
	        try
	        {
	            return parseInt( String( data ) );
	        }
	        catch( e: Error )
	        {
	        }
	        return 0;
	    }
	    
	    [Bindable("checkFunctionChanged")]
	    [Inspectable(category="Data")]
	
	    public function get checkFunction( ): Function
	    {
	        return _checkFunction;
	    }
	
	    public function set checkFunction( value: Function ): void
	    {
	        _checkFunction = value;
	        itemsSizeChanged = true;
	        invalidateDisplayList( );
	        dispatchEvent( new Event("checkFunctionChanged") );
	    }	    
		
	}
}