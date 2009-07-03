
package mundigo
{
    // itemRenderers/tree/myComponents/MyTreeItemRenderer.as
    import mx.collections.*;
    import mx.controls.Alert;
    import mx.controls.treeClasses.*;

    public class CustomTreeItemRenderer extends TreeItemRenderer
    {

        // Define the constructor.      
        public function CustomTreeItemRenderer() {
            super();
        }
        
        // Override the set method for the data property
        // to set the font color and style of each node.        
        override public function set data(value:Object):void {
            super.data = value;
          
          	if (TreeListData(super.listData).item.@type=="sousmenu")
          	{
          		setStyle("fontSize", 12);
          		setStyle("fontWeight", 'bold');
          		setStyle("textDecoration","underline");
          		setStyle("color","#b75322");
          
          	}
          	else if (TreeListData(super.listData).item.@type=="soussousmenu") {
          			setStyle("fontSize", 11);
          			setStyle("textDecoration","normal");
          			setStyle("fontWeight", 'bold');
          			setStyle("color","#d96c36");
          		}
          		else if  (TreeListData(super.listData).item.@type=="niveau4") {
          			setStyle("fontSize", 10);
          			setStyle("textDecoration","normal");
          			setStyle("fontWeight", 'normal');
          			setStyle("color","#d96c36");
          			}
          			else if  (TreeListData(super.listData).item.@type=="produit") {
          				setStyle("fontSize", 9);
          				setStyle("textDecoration","normal");
          				setStyle("fontWeight", 'normal');
          				setStyle("color","#d96c36");
          			}
        }
     
        // Override the updateDisplayList() method 
        // to set the text for each tree node.      
        override protected function updateDisplayList(unscaledWidth:Number, 
            unscaledHeight:Number):void {
       
            super.updateDisplayList(unscaledWidth, unscaledHeight);
        }
    }
}


