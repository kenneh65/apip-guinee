$(function(){

	$(function()
        {
		$(".container1").mapael(
                {
			map : {
				// Set the name of the map to display
				name : "Gainde 2000",
			}
		});
	});

	
	
	$(".container3").mapael({
		map : {
			name : "world_countries",
			defaultArea: {
				attrs : {
					fill : "#f4f4e8"
					, stroke: "#ced8d0"
				}
			}
            // Default attributes can be set for all links
            , defaultLink: {
                factor : 0.4
                , attrsHover : {
                    stroke: "#a4e100"
                }
            }
            , defaultPlot : {
                text : {
                    attrs : {
                        fill:"#000"
                    }, 
                    attrsHover : {
                        fill:"#000"
                    }
                }
            }
		},
		plots : {
			'Senegal' : {
				latitude :14.7645042, 
				longitude :-17.366028599999936, 
				tooltip: {content : "Senegal<br />Nb Investisseur: 800"}
			},
                        'France' : {
				latitude :48.86, 
				longitude :2.3444, 
				tooltip: {content : "France<br />Nb Investisseur: 850"}
			},                       
                        'Mauritanie' : {
				latitude :21.00789, 
				longitude :-10.940834999999993, 
				tooltip: {content : "Mauritanie <br />Nb Investisseur: 19"}
			},
                        'Cote d Ivoire' : {
				latitude :7.539988999999999, 
				longitude :-5.547080000000051, 
				tooltip: {content : "Cote d'ivoire <br />Nb Investisseur: 38"}
			},
                        'Nigeria' : {
				latitude :9.081999, 
				longitude :8.675277000000051, 
				tooltip: {content : "Nigeria <br />Nb Investisseur: 20"}
			},
                        'Chine' : {
				latitude :35.86166, 
				longitude :104.19539699999996, 
				tooltip: {content : "Chine <br />Nb Investisseur: 27"}
			},
                        'Inde' : {
				latitude :20.593684, 
				longitude :78.96288000000004, 
				tooltip: {content : "Inde <br />Nb Investisseur: 5"}
			},
                        'Roumanie' : {
				latitude :45.943161, 
				longitude :24.966760000000022, 
				tooltip: {content : "Roumanie <br />Nb Investisseur: 1"}
			},
                        'Canada' : {
				latitude :56.130366, 
				longitude :-106.34677099999999, 
				tooltip: {content : "Canada <br />Nb Investisseur: 3"}
			},
                        'Italie' : {
				latitude :41.87194, 
				longitude :12.567379999999957, 
				tooltip: {content : "Italie <br />Nb Investisseur: 8"}
			},
                        'Angleterre' : {
				latitude :52.3555177, 
				longitude :-1.1743197000000691, 
				tooltip: {content : "Angleterre <br />Nb Investisseur: 18"}
			},
           
            
            // Size=0 in order to make plots invisible
			'tokyo': {
				latitude :35.687418, 
				longitude :139.692306, 
				size:0,
                text : {content : ''}
			},
			'sydney' : {
				latitude :-33.917, 
				longitude :151.167,
                size:0,
                text : {content : ''}
			},
			'plot1': {
                latitude :22.906561, 
				longitude :86.840170, 
                size:0,
                text : {content : '', position : 'left', margin:5}
			},
			'plot2': {
                latitude :-0.390553, 
				longitude :115.586762, 
                size:0,
                text : {content : ''}
			},
			'plot3': {
                latitude :44.065626, 
				longitude :94.576079, 
                size:0,
                text : {content : ''}
			}
		}
       
        
        
        
	});
	
});		