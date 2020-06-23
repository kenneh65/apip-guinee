/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function readMessage(message)
{
    
    if(message)
    {
          $.ajax({
              url : route,
              type : 'POST',
              data :{message : message},
              success : function (data){
                  if(data.resultat ==1)
                  {
                      
                  }
              },
              error : function()
              {
                  alert('Erreur');
              }
          });
    }
}