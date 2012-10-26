/*
 * @author: Qennix
 * Feb 22nd, 2011
 */

//Keyboard Events
new Ext.KeyMap(document, [
{
  key: Ext.EventObject.F5,
  fn: function(keycode, e) {
    if (! e.ctrlKey) {
      if (Ext.isIE) {
        // IE6 doesn't allow cancellation of the F5 key, so trick it into
        // thinking some other key was pressed (backspace in this case)
        e.browserEvent.keyCode = 8;
      }
      e.stopEvent();
      document.location = document.location;
    }else{
      //Ext.Msg.alert('Refresh', 'You clicked: CTRL-F5');
    }
  }
}
,
{
  key: Ext.EventObject.DELETE,
  fn: function(k,e){
    iGrid = Ext.getCmp('infoGrid');
    rowSelected = iGrid.getSelectionModel().getSelected();
    if (rowSelected && !deleteButton.isDisabled()){
      deleteSkin();
    }
  }
},
{
  key: Ext.EventObject.F2,
  fn: function(k,e){
    //iGrid = Ext.getCmp('infoGrid');
    //rowSelected = iGrid.getSelectionModel().getSelected();
    //if (rowSelected){
      
    //}
  }
}
]);

var store;
var cmodel;
var infoGrid;
var viewport;
var smodel;
var newButton;
var deleteButton;
var searchButton;
var searchText;
var contextMenu;
var pageSize;

var classicSkin = '00000000000000000000000000000001';

Ext.onReady(function(){
  Ext.QuickTips.init();
  
  pageSize = parseInt(CONFIG.pageSize);
  
  newButton = new Ext.Action({
    text: _('ID_NEW'),
    iconCls: 'button_menu_ext ss_sprite  ss_add',
    disabled: false,
    handler: newSkin
  });
  
  
  deleteButton = new Ext.Action({
    text: _('ID_DELETE'),
    iconCls: 'button_menu_ext ss_sprite  ss_delete',
    handler: deleteSkin,
    disabled: true
  });
  
  
  importButton = new Ext.Action({
    text: _('ID_IMPORT'),
    iconCls: 'button_menu_ext ss_sprite ss_building_add',
    handler: importSkin,
    disabled: false
  });

  exportButton = new Ext.Action({
    text: _('ID_EXPORT'),
    iconCls: 'button_menu_ext ss_sprite ss_building_go',
    handler: exportSkin,
    disabled: true
  });
  
  searchButton = new Ext.Action({
    text: _('ID_SEARCH'),
    handler: DoSearch
  });
  
  contextMenu = new Ext.menu.Menu({
    items: [exportButton,deleteButton]
  });
  
  searchText = new Ext.form.TextField ({
    id: 'searchTxt',
    ctCls:'pm_search_text_field',
    allowBlank: true,
    width: 150,
    emptyText: _('ID_ENTER_SEARCH_TERM'),
    listeners: {
      specialkey: function(f,e){
        if (e.getKey() == e.ENTER) {
          DoSearch();
        }
      },
      focus: function(f,e) {
        var row = infoGrid.getSelectionModel().getSelected();
        infoGrid.getSelectionModel().deselectRow(infoGrid.getStore().indexOf(row));
      }
    }
  });
  
  clearTextButton = new Ext.Action({
    text: 'X',
    ctCls:'pm_search_x_button',
    handler: GridByDefault
  });
  
  
  smodel = new Ext.grid.RowSelectionModel({
    singleSelect: true,
    listeners:{
      rowselect: function(sm){
        rowSelected = infoGrid.getSelectionModel().getSelected();
        if((rowSelected.data.SKIN_FOLDER_ID)&&((rowSelected.data.SKIN_FOLDER_ID!="classic"))){
          exportButton.enable();
          deleteButton.enable();
        }else{
          exportButton.disable();
          deleteButton.disable();
        }
      },
      rowdeselect: function(sm){
        exportButton.disable();
        deleteButton.disable();
      }
    }
  });

  storeSkins  = new Ext.data.JsonStore({
    root: 'skins',
    totalProperty: 'totalCount',
    idProperty: 'SKIN_FOLDER_ID',
    autoLoad  : false,
    remoteSort: false,
    fields: [
    'SKIN_FOLDER_ID',
    'SKIN_NAME'
    ],
    proxy: new Ext.data.HttpProxy({
      url: 'skin_Ajax?action=skinList'
    })
  });
  
  store = new Ext.data.GroupingStore( {
    proxy : new Ext.data.HttpProxy({
      url: 'skin_Ajax?action=skinList'
    }),
    reader : new Ext.data.JsonReader( {
      root: 'skins',
      totalProperty: 'total_skins',
      fields : [
      {
        name : 'SKIN_ID'
      },

      {
        name : 'SKIN_FOLDER_ID'
      },

      {
        name : 'SKIN_NAME'
      },

      {
        name : 'SKIN_DESCRIPTION'
      },

      {
        name : 'SKIN_AUTHOR'
      },

      {
        name : 'SKIN_CREATEDATE'
      },

      {
        name : 'SKIN_MODIFIEDDATE'
      },

      {
        name : 'SKIN_STATUS'
      }
      ]
    })
  });
  
  cmodel = new Ext.grid.ColumnModel({
    defaults: {
      width: 50,
      sortable: true
    },
    columns: [
    {
      id:'SKIN_UID',
      dataIndex: 'SKIN_UID',
      hidden:true,
      hideable:false
    },

    {
      header: _('ID_NAME'),
      dataIndex: 'SKIN_NAME',
      width: 80,
      align:'left'
    },

    {
      header: _('ID_DESCRIPTION'),
      dataIndex: 'SKIN_DESCRIPTION',
      width: 200,
      align:'left'
    },

    {
      header: _('ID_AUTHOR'),
      dataIndex: 'SKIN_AUTHOR',
      width: 80,
      align:'left'
    },

    {
      header: _('ID_CREATE'),
      dataIndex: 'SKIN_CREATEDATE',
      width: 50,
      align:'center',
      renderer: showdate
    },

    {
      header: _('ID_UPDATE_DATE'),
      dataIndex: 'SKIN_MODIFIEDDATE',
      width: 50,
      align:'center',
      renderer: showdate
    }
    
    ]
  });
  
  storePageSize = new Ext.data.SimpleStore({
    fields: ['size'],
    data: [['20'],['30'],['40'],['50'],['100']],
    autoLoad: true
  });
  
  comboPageSize = new Ext.form.ComboBox({
    typeAhead     : false,
    mode          : 'local',
    triggerAction : 'all',
    store: storePageSize,
    valueField: 'size',
    displayField: 'size',
    width: 50,
    editable: false,
    listeners:{
      select: function(c,d,i){
        UpdatePageConfig(d.data['size']);
        bbarpaging.pageSize = parseInt(d.data['size']);
        bbarpaging.moveFirst();
      }
    }
  });
  
  comboPageSize.setValue(pageSize);
  
  bbarpaging = new Ext.PagingToolbar({
    pageSize: pageSize,
    store: store,
    displayInfo: true,
    displayMsg: _('ID_GRID_PAGE_DISPLAYING_SKIN_MESSAGE') + '&nbsp; &nbsp; ',
    emptyMsg: _('ID_GRID_PAGE_NO_SKIN_MESSAGE')//,
  //items: ['-',_('ID_PAGE_SIZE')+':',comboPageSize]
  });
  
  
  infoGrid = new Ext.grid.GridPanel({
    region: 'center',
    layout: 'fit',
    id: 'infoGrid',
    height:100,
    autoWidth : true,
    stateful : true,
    stateId : 'grid',
    enableColumnResize: true,
    enableHdMenu: true,
    frame:false,
    //iconCls:'icon-grid',
    columnLines: false,
    viewConfig: {
      forceFit:true
    },
    title : _('ID_SKINS'),
    store: store,
    cm: cmodel,
    sm: smodel,
    tbar: [newButton, '-', importButton,exportButton,'-',deleteButton, {
      xtype: 'tbfill'
    }, searchText,clearTextButton,searchButton],
    bbar: bbarpaging,
    listeners: {
      rowdblclick: function(grid, n,e){
        rowSelected = infoGrid.getSelectionModel().getSelected();        
        if((rowSelected.data.SKIN_FOLDER_ID)&&((rowSelected.data.SKIN_FOLDER_ID!=""))){
          viewport.getEl().mask(_('ID_SKIN_SWITCHING'));
          changeSkin(rowSelected.data.SKIN_FOLDER_ID,SYS_SKIN);
        }
      }
    },
    view: new Ext.grid.GroupingView({
      forceFit:true,
      groupTextTpl: '{text}'
    })
  });
  
  infoGrid.on('rowcontextmenu', 
    function (grid, rowIndex, evt) {
      var sm = grid.getSelectionModel();
      sm.selectRow(rowIndex, sm.isSelected(rowIndex));
    },
    this
    );
  
  infoGrid.on('contextmenu', function(evt){
    evt.preventDefault();
  }, this);
  infoGrid.addListener('rowcontextmenu',onMessageContextMenu, this);
  infoGrid.store.load();
  
  viewport = new Ext.Viewport({
    layout: 'fit',
    autoScroll: false,
    items: [
    infoGrid
    ]
  });
});


//Function format dates
showdate = function (value){
  return _DF(value);
};

//Funtion Handles Context Menu Opening
onMessageContextMenu = function (grid, rowIndex, e) {
  e.stopEvent();
  var coords = e.getXY();
  contextMenu.showAt([coords[0], coords[1]]);
};

//Do Nothing Function
DoNothing = function(){};

newSkin = function(){
  newDialog = new Ext.Window( {
    id: "newDialog",
    title:_('ID_NEW_SKIN'),
    autoCreate: true,
    modal:true,
    width:400,
    autoHeight:true,
    shadow:true,
    minWidth:100,
    minHeight:50,
    proxyDrag: true,
    resizable: true,
    keys: {
      key: 27,
      fn  : function(){
        newDialog.hide();
      }
    },
    items:[
    {
      xtype:'form',
      autoScroll:true,
      autoHeight:true,
      id:"newform",
      fileUpload:true,
      labelWidth:100,
      url:'skin_Ajax',      
      frame:false,
      items:[
      {
        xtype:'textfield',
        id:'skinName',
        allowBlank: false,
        width:200,
        emptyText: _('ID_SKIN_NAME_REQUIRED'),
        fieldLabel:_('ID_NAME'),
        enableKeyEvents:true,
        vtype:'alphanum',
        listeners: {
          keyup:function(a,b,c){
            
            Ext.getCmp('newform').getForm().findField('skinFolder').setValue(a.getValue().toLowerCase());
          }
             
        }
      },
      {
        xtype:'textfield',
        emptyText: _('ID_SKIN_FOLDER_REQUIRED'),
        id:'skinFolder',
        fieldLabel:_('ID_SKIN_FOLDER'),
        width:200,
        vtype:'alphanum',
        autoCreate: {
          tag: 'input',
          type: 'text',
          size: '20',
          autocomplete: 'off',
          maxlength: '10'
        }
      },
      {
        xtype:'textarea',
        id:'skinDescription',
        fieldLabel:_('ID_DESCRIPTION'),
        width:200
      },
      {
        xtype:'textfield',
        id:'skinAuthor',
        emptyText :'ProcessMaker Team',
        value :'ProcessMaker Team',
        allowBlank: false,
        width:200,
        fieldLabel:_('ID_AUTHOR')
      },
      new Ext.form.ComboBox({
        id:'skinBase',
        fieldLabel : _('ID_SKIN_BASE'),
        editable : false,
        store: storeSkins,
        valueField    :'SKIN_FOLDER_ID',
        displayField  :'SKIN_NAME',
        submitValue:true,
        typeAhead     : false,
        triggerAction : 'all',
        allowBlank: false,
        selectOnFocus:true,
        width:200
      })
      ],
      buttons:[
      {
        text:_('ID_SAVE'),
        handler: function() {
          //statusBarMessage( _('ID_UPLOADING_FILE'), true, true );
          newDialog.getEl().mask(_('ID_SKIN_CREATING'));
          form = Ext.getCmp("newform").getForm();

          //Ext.getCmp("uploadform").getForm().submit();
          //console.log(form);
          //console.log(form.url);
          Ext.getCmp("newform").getForm().submit({
            //reset: true,
            reset: false,
            success: function(form, action) {

              store.reload();

              Ext.getCmp("newDialog").destroy();
              PMExt.notify(_('ID_SKINS'),_('ID_SKIN_SUCCESS_CREATE'));
            },
            failure: function(form, action) {
              Ext.getCmp("newDialog").destroy();

              if( !action.result ) {
                Ext.MessageBox.alert("error", _('ID_ERROR'));
                return;
              }
              Ext.MessageBox.alert("error", action.result.error);

            },
            scope: Ext.getCmp("newform"),
            // add some vars to the request, similar to hidden fields
            params: {             
              action: "newSkin",
              requestType: "xmlhttprequest"
            }
          });
        }
      },
      {
        text:_('ID_CANCEL'),
        handler: function() {
          Ext.getCmp("newDialog").destroy();
        }
      }
      ]
    }
    ]

  });
  newDialog.on( 'hide', function() {
    newDialog.destroy(true);
  } );
  newDialog.show();
}
importSkin = function(){
  importDialog = new Ext.Window( {
    id: "importDialog",
    title:_('ID_UPLOAD'),
    autoCreate: true,
    modal:true,
    width:400,
    autoHeight:true,
    shadow:true,
    minWidth:100,
    minHeight:50,
    proxyDrag: true,
    resizable: true,
    keys: {
      key: 27,
      fn  : function(){
        importDialog.hide();
      }
    },
    items:[
    {
      xtype:'form',
      autoScroll:true,
      autoHeight:true,
      id:"uploadform",
      fileUpload:true,
      labelWidth:90,
      url:'skin_Ajax',
      //tooltip:"Max File Size <strong>XXX MB</strong><br />Max Post Size<strong>XXX MB</strong><br />",
      frame:false,
      items:[
      //{
      //  xtype:"displayfield",
      //  value:"Max File Size <strong>XXX MB</strong><br />Max Post Size<strong>XXX MB</strong><br />"
      //},
      {
        xtype:"fileuploadfield",
        fieldLabel: _('ID_FILE'),
        id:         "uploadedFile",
        name:       "uploadedFile",
        width:      190,
        buttonText: _('ID_BROWSE'),
        buttonOnly: false
      },
      {
        xtype:      "checkbox",
        fieldLabel: _('ID_OVERWRITE'),
        name:       "overwrite_files",
        checked:    true
      }
      ],
      buttons:[
      {
        text:_('ID_SAVE'),
        handler: function() {
          //statusBarMessage( _('ID_UPLOADING_FILE'), true, true );
          importDialog.getEl().mask(_('ID_SKIN_IMPORTING'));
          form = Ext.getCmp("uploadform").getForm();

          //Ext.getCmp("uploadform").getForm().submit();
          //console.log(form);
          //console.log(form.url);
          Ext.getCmp("uploadform").getForm().submit({
            //reset: true,
            reset: false,
            success: function(form, action) {

              store.reload();
                     
              Ext.getCmp("importDialog").destroy();
              PMExt.notify(_('ID_SKINS'),_('ID_SKIN_SUCCESS_IMPORTED'));
            },
            failure: function(form, action) {
              Ext.getCmp("importDialog").destroy();

              if( !action.result ) {
                Ext.MessageBox.alert("error", _('ID_ERROR'));
                return;
              }
              Ext.MessageBox.alert("error", action.result.error);
                     
            },
            scope: Ext.getCmp("uploadform"),
            // add some vars to the request, similar to hidden fields
            params: {
              option: "standardupload",
              action: "importSkin",
              requestType: "xmlhttprequest",
              confirm: "true"
            }
          });
        }
      },
      {
        text:_('ID_CANCEL'),
        handler: function() {
          Ext.getCmp("importDialog").destroy();
        }
      }
      ]
    }
    ]

  });
  // importDialog.doLayout();
  
  // recalculate
  // Window size
  //importDialog.syncSize();
  // center the window
  //importDialog.center();
  importDialog.on( 'hide', function() {
    importDialog.destroy(true);
  } );
  importDialog.show();
};
exportSkin = function(){
  viewport.getEl().mask(_('ID_SKIN_EXPORTING'));
  rowSelected = infoGrid.getSelectionModel().getSelected();
  if((rowSelected.data.SKIN_FOLDER_ID)&&((rowSelected.data.SKIN_FOLDER_ID!="classic"))){
    Ext.Ajax.request({
      url: 'skin_Ajax',
      params: {
        action: 'exportSkin',
        SKIN_FOLDER_ID: rowSelected.data.SKIN_FOLDER_ID
      },
      success: function(r,o){
        viewport.getEl().unmask();
        var resp = Ext.util.JSON.decode(r.responseText);
        if (resp.success){                    
          try {
            Ext.destroy(Ext.get('downloadIframe'));
          }
          catch(e) {}
          Ext.DomHelper.append(document.body, {
            tag: 'iframe',
            id:'downloadIframe',
            frameBorder: 0,
            width: 0,
            height: 0,
            css: 'display:none;visibility:hidden;height:0px;',
            src: 'skin_Ajax?action=streamSkin&file='+resp.message
          });
          viewport.getEl().unmask();
        }else{
          viewport.getEl().unmask();
          Ext.Msg.alert('Alert', resp.message);
        //PMExt.error(_('ID_SKINS'),_('ID_MSG_CANNOT_EXPORT_SKIN'));
        }
      },
      failure: function(r,o){
        viewport.getEl().unmask();
        PMExt.error(_('ID_SKINS'),_('ID_MSG_CANNOT_EXPORT_SKIN'));
      }
    });
  }else{
    PMExt.error(_('ID_SKINS'),_('ID_MSG_CANNOT_EXPORT_DEFAULT_SKIN'));
  }
}



//Load Grid By Default
GridByDefault = function(){
  searchText.reset();
  infoGrid.store.load();
};

//Do Search Function
DoSearch = function(){
  infoGrid.store.load({
    params: {
      textFilter: searchText.getValue()
    }
  });
};



deleteSkin = function(){
  Ext.Msg.confirm(_('ID_CONFIRM'),_('ID_CONFIRM_DELETE_SKIN'),
          function(btn, text){
            if (btn=='yes'){
              viewport.getEl().mask(_('ID_PROCESSING'));
              Ext.Ajax.request({
                url: 'skin_Ajax',
                params: {
                  action: 'deleteSkin',
                  SKIN_FOLDER_ID: rowSelected.data.SKIN_FOLDER_ID
                },
                success: function(r,o){
                  viewport.getEl().unmask();
                  deleteButton.disable();

                  store.reload();
                  PMExt.notify(_('ID_SKINS'),_('ID_SKIN_SUCCESS_DELETE'));
                },
                failure: function(r,o){
                  viewport.getEl().unmask();
                }
              });
            }
          }
          );
}



//Update Page Size Configuration
UpdatePageConfig = function(pageSize){
  Ext.Ajax.request({
    url: 'calendar_Ajax',
    params: {
      action:'updatePageSize',
      size: pageSize
    }
  });
};

function changeSkin(newSkin,currentSkin){
  currentLocation=top.location.href;
  newLocation = currentLocation.replace("/"+currentSkin+"/","/"+newSkin+"/");
  top.location.href=newLocation;
}