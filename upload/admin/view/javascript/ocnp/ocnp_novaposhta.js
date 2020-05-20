function OCNP_UserMessage(parent){

   this.success = function(message){
      show(message, 'fa-check-circle', 'alert-success');
   }

   this.error = function(message){
      show(message, 'fa-exclamation-circle', 'alert-danger');
   }

   function show(message, icon_class, class_id){
      parent.insertBefore(createControl(message, icon_class, class_id), parent.firstChild);
   }

   function Icon(class_id){
      var icon = document.createElement('i');
      icon.classList.add('fa', class_id);
      return icon;
   }

   function createControl(message, icon_class, class_id){
      var control = document.createElement('div');
      control.appendChild(Icon(icon_class));
      control.appendChild(document.createTextNode(message));
      control.classList.add('alert', 'alert-dismissible', class_id);
      control.appendChild(closeButton());
      return control;
   }

   function closeButton(){
      var button = document.createElement('button');
      button.type = 'button';
      button.setAttribute('data-dismiss', 'alert');
      button.classList.add('close');
      button.innerText = '×';
      return button;
   }
}

function OCNP_Request(name){
   var m_token = 'user_token='+ getURLVar('user_token');
   var m_route = 'route=extension/shipping/ocnp_novaposhta/' + name;

   this.getURL = function(){
      return ('index.php?' + m_route + '&' + m_token);
   }
}

function OCNP_Server(){
   var m_console = new OCNP_UserMessage(document.getElementsByClassName('panel-body')[0]);

   this.sendRequest = function(request, callback){
      $.ajax({
         url: request.getURL(),
         dataType: 'json',
         success: function(response){
            showServerMessage(response);
            if (response.success && callback){
               callback(response);
            }
         }
      });
   }

   function showServerMessage(response){
      if (response.message){
         if (response.success){
            m_console.success(response.message);
         }
         else{
            m_console.error(response.message);
         }
      }
   }
}

function SyncCities(){
   var server = new OCNP_Server();
   server.sendRequest(new OCNP_Request('syncCities'), function(response){
      console.log('callback');
   });
}