function getStateBorder(state)
{
	var stateBoundaries = new Array();
	
	//Alabama
    if((state == 'AL')||(state=='Alabama'))
    stateBoundaries = [  
        new google.maps.LatLng(34.984 ,-85.605),   //top right
        new google.maps.LatLng(32.864 ,-85.184 ),  //mid right
        new google.maps.LatLng(32.426 ,-84.964 ), 
        new google.maps.LatLng(32.326 ,-85.006 ), 
        new google.maps.LatLng(32.261 ,-84.889 ), 
        new google.maps.LatLng(32.133 ,-85.062 ),  
        new google.maps.LatLng(31.84 , -85.141 ),
        new google.maps.LatLng(31.538 ,-85.041 ), 
        new google.maps.LatLng(31.188 ,-85.108 ),
        new google.maps.LatLng(31, -85.002),       //bottom right 
        new google.maps.LatLng(30.997 ,-87.598),   //bottom mid
        new google.maps.LatLng(30.846 ,-87.625 ),
        new google.maps.LatLng(30.655 ,-87.397 ),
        new google.maps.LatLng(30.466 ,-87.426),
        new google.maps.LatLng(30.348 ,-88.394),   //bottom left
        new google.maps.LatLng(31.894 ,-88.473 ),
        new google.maps.LatLng(32.732 ,-88.37 ),
        new google.maps.LatLng(32.747 ,-88.368 ),
        new google.maps.LatLng(34.892 ,-88.097),
        new google.maps.LatLng(34.995 ,-88.2)      //top left
	  ];

	//Alaska
	else if((state == 'AK')||(state=='Alaska'))
    stateBoundaries = [  
      new google.maps.LatLng(69.64673,-141.002741),  //top right
      new google.maps.LatLng(60.306142,-141.00184),  
      new google.maps.LatLng(60.2243,-140.535049),
      new google.maps.LatLng(60.310287,-140.472221),
      new google.maps.LatLng(60.185233,-139.989166),
      new google.maps.LatLng(60.340202,-139.698372),
      new google.maps.LatLng(60.357357,-139.086227),
      new google.maps.LatLng(60.323718,-139.082193),
      new google.maps.LatLng(60.090722,-139.200296),
      new google.maps.LatLng(60.010915,-139.060907),
      new google.maps.LatLng(60.006753,-139.056959),
      new google.maps.LatLng(60.001904,-139.053526),
      new google.maps.LatLng(59.998171,-139.046316),
      new google.maps.LatLng(59.993696,-139.031639),
      new google.maps.LatLng(59.928743,-138.796206),
      new google.maps.LatLng(59.910244,-138.702092),
      new google.maps.LatLng(59.813756,-138.662653),
      new google.maps.LatLng(59.810261,-138.66291),
      new google.maps.LatLng(59.782237,-138.631024),
      new google.maps.LatLng(59.77055,-138.620939),
      new google.maps.LatLng(59.243064,-137.60376),
      new google.maps.LatLng(58.986645,-137.498703),
      new google.maps.LatLng(58.90684,-137.526405),
      new google.maps.LatLng(58.909488,-137.447398),
      new google.maps.LatLng(59.00234,-137.264814),
      new google.maps.LatLng(59.138437,-136.863899),
      new google.maps.LatLng(59.158376,-136.826649),
      new google.maps.LatLng(59.164909,-136.581527),
      new google.maps.LatLng(59.261121,-136.486673),
      new google.maps.LatLng(59.272986,-136.494098),
      new google.maps.LatLng(59.284266,-136.466804),
      new google.maps.LatLng(59.464137,-136.474314),
      new google.maps.LatLng(59.448009,-136.365824),
      new google.maps.LatLng(59.449744,-136.358375),  //Haines Hwy
      new google.maps.LatLng(59.464181,-136.30188),
      new google.maps.LatLng(59.524612,-136.234331),
      new google.maps.LatLng(59.538147,-136.236477),
      new google.maps.LatLng(59.558679,-136.237335),
      new google.maps.LatLng(59.573505,-136.278448),
      new google.maps.LatLng(59.58802,-136.318789),
      new google.maps.LatLng(59.599271,-136.350374),
      new google.maps.LatLng(59.60774,-136.318359),
      new google.maps.LatLng(59.623412,-136.257505),
      new google.maps.LatLng(59.639858,-136.190472),
      new google.maps.LatLng(59.646755,-136.120777),
      new google.maps.LatLng(59.654734,-136.040268),
      new google.maps.LatLng(59.657856,-136.007824),
      new google.maps.LatLng(59.663796,-135.945854),
      new google.maps.LatLng(59.799771,-135.477562),
      new google.maps.LatLng(59.701187,-135.254402),
      new google.maps.LatLng(59.697896,-135.243759),
      new google.maps.LatLng(59.697896,-135.234318),
      new google.maps.LatLng(59.697116,-135.231228),
      new google.maps.LatLng(59.664446,-135.214233),
      new google.maps.LatLng(59.632005,-135.166512),
      new google.maps.LatLng(59.630703,-135.162907),
      new google.maps.LatLng(59.625148,-135.153122),
      new google.maps.LatLng(59.623412,-135.114326),
      new google.maps.LatLng(59.563723,-135.027466),
      new google.maps.LatLng(59.474646,-135.026264),
      new google.maps.LatLng(59.453364,-135.071068),
      new google.maps.LatLng(59.427792,-135.097675),
      new google.maps.LatLng(59.392587,-135.003433),
      new google.maps.LatLng(59.387867,-134.993305),
      new google.maps.LatLng(59.37554,-135.004292),
      new google.maps.LatLng(59.345357,-135.029011),
      new google.maps.LatLng(59.280265,-134.961891),
      new google.maps.LatLng(59.261055,-134.808083),
      new google.maps.LatLng(59.247716,-134.702511),
      new google.maps.LatLng(59.228839,-134.695473),
      new google.maps.LatLng(59.19079,-134.681911),
      new google.maps.LatLng(59.128309,-134.566383),
      new google.maps.LatLng(59.128133,-134.481239),
      new google.maps.LatLng(59.082652,-134.441757),
      new google.maps.LatLng(59.035079,-134.379616),
      new google.maps.LatLng(58.996549,-134.400043),
      new google.maps.LatLng(58.976383,-134.400902),
      new google.maps.LatLng(58.959127,-134.306488),
      new google.maps.LatLng(58.919537,-134.328461),
      new google.maps.LatLng(58.857981,-134.250526),
      new google.maps.LatLng(58.794892,-134.054832),
      new google.maps.LatLng(58.727946,-133.840599),
      new google.maps.LatLng(58.607439,-133.700523),
      new google.maps.LatLng(58.520775,-133.557358),
      new google.maps.LatLng(58.428145,-133.379517),
      new google.maps.LatLng(58.385518,-133.461227),
      new google.maps.LatLng(58.382818,-133.459511),
      new google.maps.LatLng(58.283869,-133.358574),
      new google.maps.LatLng(58.271052,-133.344154),
      new google.maps.LatLng(58.195319,-133.235321),
      new google.maps.LatLng(58.150236,-133.175926),
      new google.maps.LatLng(57.999912,-133.076706),
      new google.maps.LatLng(57.934902,-132.990189),
      new google.maps.LatLng(57.84402,-132.870369),
      new google.maps.LatLng(57.704514,-132.756386),
      new google.maps.LatLng(57.620221,-132.658539),
      new google.maps.LatLng(57.503651,-132.558975),
      new google.maps.LatLng(57.385228,-132.412376),
      new google.maps.LatLng(57.349497,-132.368431),
      new google.maps.LatLng(57.215705,-132.252731),
      new google.maps.LatLng(57.09523,-132.370834),
      new google.maps.LatLng(57.050628,-132.051201),
      new google.maps.LatLng(56.874497,-132.125702),
      new google.maps.LatLng(56.804826,-131.871986),
      new google.maps.LatLng(56.753288,-131.901512),
      new google.maps.LatLng(56.704317,-131.861687),
      new google.maps.LatLng(56.661321,-131.84967),
      new google.maps.LatLng(56.602027,-131.834908),
      new google.maps.LatLng(56.603728,-131.76384),
      new google.maps.LatLng(56.613365,-131.580849),
      new google.maps.LatLng(56.547372,-131.460686),
      new google.maps.LatLng(56.448641,-131.168518),
      new google.maps.LatLng(56.406874,-131.086121),
      new google.maps.LatLng(56.398325,-131.018486),
      new google.maps.LatLng(56.373807,-130.828629),
      new google.maps.LatLng(56.367342,-130.78228),
      new google.maps.LatLng(56.268333,-130.622635),
      new google.maps.LatLng(56.248119,-130.540581),
      new google.maps.LatLng(56.239534,-130.466766),
      new google.maps.LatLng(56.140768,-130.425224),
      new google.maps.LatLng(56.126993,-130.343513),
      new google.maps.LatLng(56.096747,-130.245667),
      new google.maps.LatLng(56.116658,-130.102501),
      new google.maps.LatLng(56.039829,-130.035553),
      new google.maps.LatLng(56.037336,-130.031776),
      new google.maps.LatLng(56.017383,-130.01667),
      new google.maps.LatLng(55.993197,-130.003967),
      new google.maps.LatLng(55.984747,-130.01564),
      new google.maps.LatLng(55.96496,-130.020103),
      new google.maps.LatLng(55.915929,-130.02594),
      new google.maps.LatLng(55.915544,-130.021133),
      new google.maps.LatLng(55.916506,-130.012894),
      new google.maps.LatLng(55.824045,-130.083961),
      new google.maps.LatLng(55.806877,-130.123787),
      new google.maps.LatLng(55.801667,-130.12825),
      new google.maps.LatLng(55.767303,-130.150223),
      new google.maps.LatLng(55.715121,-130.14782),
      new google.maps.LatLng(55.681843,-130.111427),
      new google.maps.LatLng(55.581644,-130.126534),
      new google.maps.LatLng(55.562427,-130.119324),
      new google.maps.LatLng(55.491304,-130.084991),
      new google.maps.LatLng(55.4518,-130.044136),
      new google.maps.LatLng(55.338127,-130.023193),
      new google.maps.LatLng(55.301988,-129.981995),
      new google.maps.LatLng(55.28459,-129.979248),
      new google.maps.LatLng(55.277355,-129.985085),
      new google.maps.LatLng(55.198271,-130.095978),
      new google.maps.LatLng(55.12433,-130.152626),
      new google.maps.LatLng(55.093105,-130.182495),
      new google.maps.LatLng(55.064804,-130.187302),
      new google.maps.LatLng(54.988055,-130.258369),
      new google.maps.LatLng(54.921026,-130.33905),
      new google.maps.LatLng(54.839652,-130.471916),
      new google.maps.LatLng(54.791185,-130.568733),
      new google.maps.LatLng(54.777722,-130.637398),
      new google.maps.LatLng(54.76168,-130.656967),
      new google.maps.LatLng(54.737902,-130.626411),
      new google.maps.LatLng(54.723431,-130.628815),
      new google.maps.LatLng(54.70578,-130.615425),    //bottom right
      new google.maps.LatLng(54.514704,-133.599243),
      new google.maps.LatLng(57.833055,-137.329102),
      new google.maps.LatLng(59.142135,-140.053711),
      new google.maps.LatLng(59.712097,-142.998047),
      new google.maps.LatLng(59.667741,-145.821533),
      new google.maps.LatLng(59.321981,-148.85376),
      new google.maps.LatLng(58.20545,-151.325684),
      new google.maps.LatLng(57.20771,-152.116699),
      new google.maps.LatLng(55.166319,-155.544434),
      new google.maps.LatLng(50.736455,-170.771484),
      new google.maps.LatLng(48.341646,-179.824219),
      new google.maps.LatLng(52.908902,170.507813),     //Western Most point
      new google.maps.LatLng(61.856149,-177.363281),
      new google.maps.LatLng(65.512963,-169.101562),
      new google.maps.LatLng(68.204212,-168.925781),
      new google.maps.LatLng(71.746432,-157.060547),
      new google.maps.LatLng(70.281704,-142.712402)
    ];
  
    //Arkansas
    else if((state == 'AR')||(state=='Arkansas'))
    stateBoundaries = [  
        new google.maps.LatLng(33.004 ,-91.166),   //bottom right
        new google.maps.LatLng(33.059 ,-91.121),
        new google.maps.LatLng(33.125 ,-91.201),
        new google.maps.LatLng(33.136 ,-91.093),
        new google.maps.LatLng(33.275 ,-91.043),
        new google.maps.LatLng(33.301 ,-91.141),
        new google.maps.LatLng(33.449 ,-91.059),
        new google.maps.LatLng(33.378 ,-91.148),
        new google.maps.LatLng(33.407 ,-91.209),
        new google.maps.LatLng(33.473 ,-91.125),
        new google.maps.LatLng(33.437 ,-91.231),
        new google.maps.LatLng(33.561 ,-91.23),
        new google.maps.LatLng(33.607 ,-91.129),
        new google.maps.LatLng(33.69 ,-91.222),
        new google.maps.LatLng(33.681 ,-91.037),
        new google.maps.LatLng(33.726 ,-91.14),
        new google.maps.LatLng(33.783 ,-90.986),
        new google.maps.LatLng(33.967 ,-91.09),
        new google.maps.LatLng(34.026 ,-90.893),
        new google.maps.LatLng(34.164 ,-90.927),
        new google.maps.LatLng(34.159 ,-90.809),
        new google.maps.LatLng(34.238 ,-90.934),
        new google.maps.LatLng(34.207 ,-90.848),
        new google.maps.LatLng(34.364 ,-90.762),
        new google.maps.LatLng(34.314 ,-90.661),
        new google.maps.LatLng(34.418 ,-90.573),
        new google.maps.LatLng(34.689 ,-90.569),
        new google.maps.LatLng(34.624 ,-90.535),
        new google.maps.LatLng(34.684 ,-90.461),
        new google.maps.LatLng(34.711 ,-90.567),
        new google.maps.LatLng(34.785 ,-90.547),
        new google.maps.LatLng(34.737 ,-90.459),
        new google.maps.LatLng(34.892 ,-90.464),
        new google.maps.LatLng(34.848 ,-90.306),
        new google.maps.LatLng(34.924 ,-90.241),
        new google.maps.LatLng(34.995 ,-90.308), // bottom of tennessee border
        
        new google.maps.LatLng(35.139 ,-90.065),     
        new google.maps.LatLng(35.265 ,-90.16),
        new google.maps.LatLng(35.277 ,-90.168),
        new google.maps.LatLng(35.386 ,-90.08),
        new google.maps.LatLng(35.421 ,-90.169),
        new google.maps.LatLng(35.429 ,-90.002),
        new google.maps.LatLng(35.547 ,-90.04),
        new google.maps.LatLng(35.514 ,-89.916),
        new google.maps.LatLng(35.592 ,-89.955),
        new google.maps.LatLng(35.639 ,-89.853),
        new google.maps.LatLng(35.729 ,-89.957),
        new google.maps.LatLng(35.814 ,-89.71),
        new google.maps.LatLng(35.909 ,-89.742),
        new google.maps.LatLng(35.892 ,-89.642),
        new google.maps.LatLng(36.001 ,-89.733),   //top right
        new google.maps.LatLng(35.996 ,-90.377),  
        new google.maps.LatLng(36.303 ,-90.064),
        new google.maps.LatLng(36.497 ,-90.151),
        new google.maps.LatLng(36.499 ,-90.789),
        new google.maps.LatLng(36.499 ,-90.802),
        new google.maps.LatLng(36.499 ,-94.617),   //top left
        new google.maps.LatLng(35.398 ,-94.431),
        new google.maps.LatLng(33.637 ,-94.485),
        new google.maps.LatLng(33.545 ,-94.386),
        new google.maps.LatLng(33.551 ,-94.043),
        new google.maps.LatLng(33.019 ,-94.042)   //bottom left
    ];

    //Arizona
    else if((state == 'AZ')||(state=='Arizona'))
    stateBoundaries = [  
        new google.maps.LatLng(37 ,-114.049),            //top left
        new google.maps.LatLng(36.194 ,-114.045), 
        new google.maps.LatLng(36.016 ,-114.21), 
        new google.maps.LatLng(36.142 ,-114.369), 
        new google.maps.LatLng(36.103 ,-114.735), 
        new google.maps.LatLng(35.804 ,-114.71), 
        new google.maps.LatLng(35.79 ,-114.698), 
        new google.maps.LatLng(35.181 ,-114.568), 
        new google.maps.LatLng(35.001 ,-114.632),        //top of cali section
        new google.maps.LatLng(34.456 ,-114.384), 
        new google.maps.LatLng(34.261 ,-114.129), 
        new google.maps.LatLng(34.106 ,-114.415), 
        new google.maps.LatLng(33.937 ,-114.533), 
        new google.maps.LatLng(33.554 ,-114.524), 
        new google.maps.LatLng(33.406 ,-114.723), 
        new google.maps.LatLng(33.304 ,-114.73), 
        new google.maps.LatLng(33.088 ,-114.706), 
        new google.maps.LatLng(33.024 ,-114.51), 
        new google.maps.LatLng(32.845 ,-114.469), 
        new google.maps.LatLng(32.719 ,-114.718),        //bottom of cali section
        new google.maps.LatLng(32.619135,-114.809875),
        new google.maps.LatLng(32.488914,-114.807129),   //bottom left
        new google.maps.LatLng(31.332525,-111.071777),
        new google.maps.LatLng(31.332 ,-109.049),        //bottom right
        new google.maps.LatLng(34.96 ,-109.045),
        new google.maps.LatLng(36.002 ,-109.045),
        new google.maps.LatLng(36.999 ,-109.044)         //top right
    ];

    //California
    else if((state == 'CA')||(state=='California'))
    stateBoundaries = [  
        new google.maps.LatLng(41.998284, -124.212112),        //top left
        new google.maps.LatLng(41.995, -119.998),        //top right
        new google.maps.LatLng(39, -120),
        new google.maps.LatLng(36.972, -117.166),
        new google.maps.LatLng(35.001, -114.632),        //top of AZ section
        new google.maps.LatLng(34.456, -114.384),
        new google.maps.LatLng(34.261, -114.129),
        new google.maps.LatLng(34.106, -114.415),
        new google.maps.LatLng(33.937, -114.533),
        new google.maps.LatLng(33.554, -114.524),
        new google.maps.LatLng(33.406, -114.723),
        new google.maps.LatLng(33.304, -114.73),
        new google.maps.LatLng(33.088, -114.706),
        new google.maps.LatLng(33.024, -114.51),
        new google.maps.LatLng(32.845, -114.469),
        new google.maps.LatLng(32.719, -114.718),        //bottom right
        new google.maps.LatLng(32.534151, -117.124472), //bottom left
        new google.maps.LatLng(32.542255, -117.124729),
        new google.maps.LatLng(32.557665, -117.131081),
        new google.maps.LatLng(32.564971, -117.133055),
        new google.maps.LatLng(32.577051, -117.133226),
        new google.maps.LatLng(32.611978, -117.135544),
        new google.maps.LatLng(32.646891, -117.149792),
        new google.maps.LatLng(32.671677, -117.169619),
        new google.maps.LatLng(32.678396, -117.179918),
        new google.maps.LatLng(32.687137, -117.197256),
        new google.maps.LatLng(32.686631, -117.214079),
        new google.maps.LatLng(32.681782, -117.22351),
        new google.maps.LatLng(32.671279, -117.236223),
        new google.maps.LatLng(32.668425, -117.237082),
        new google.maps.LatLng(32.665716, -117.239184),
        new google.maps.LatLng(32.664813, -117.242746),
        new google.maps.LatLng(32.666366, -117.244678),
        new google.maps.LatLng(32.68273, -117.24957),
        new google.maps.LatLng(32.69204, -117.253422),
        new google.maps.LatLng(32.694803, -117.254237),
        new google.maps.LatLng(32.699353, -117.255642),
        new google.maps.LatLng(32.703488, -117.255739),
        new google.maps.LatLng(32.714782, -117.256629),
        new google.maps.LatLng(32.719584, -117.25738),
        new google.maps.LatLng(32.725361, -117.25841),
        new google.maps.LatLng(32.730613, -117.257359),
        new google.maps.LatLng(32.733357, -117.257445),
        new google.maps.LatLng(32.736218, -117.2562),
        new google.maps.LatLng(32.744963, -117.255621),
        new google.maps.LatLng(32.754185, -117.253132),
        new google.maps.LatLng(32.756151, -117.252402),
        new google.maps.LatLng(32.759003, -117.254119),
        new google.maps.LatLng(32.768819, -117.253218),
        new google.maps.LatLng(32.778651, -117.253625),
        new google.maps.LatLng(32.795825, -117.257102),
        new google.maps.LatLng(32.801759, -117.259698), // palisades Park
        new google.maps.LatLng(32.807512, -117.264655),
        new google.maps.LatLng(32.807557, -117.26724),
        new google.maps.LatLng(32.807864, -117.267938),
        new google.maps.LatLng(32.814059, -117.273399),
        new google.maps.LatLng(32.815402, -117.274075),
        new google.maps.LatLng(32.817728, -117.273592),
        new google.maps.LatLng(32.818125, -117.27562),
        new google.maps.LatLng(32.81891, -117.276843),
        new google.maps.LatLng(32.820641, -117.279772),
        new google.maps.LatLng(32.82202, -117.281027),
        new google.maps.LatLng(32.827493, -117.28019),
        new google.maps.LatLng(32.832568, -117.28225),
        new google.maps.LatLng(32.847577, -117.278366),
        new google.maps.LatLng(32.870793, -117.253132),
        new google.maps.LatLng(32.915188, -117.258453),
        new google.maps.LatLng(32.959994, -117.268581),
        new google.maps.LatLng(32.989876, -117.27416),
        new google.maps.LatLng(33.077231, -117.310638),
        new google.maps.LatLng(33.195029, -117.385569),
        new google.maps.LatLng(33.347307, -117.524099),
        new google.maps.LatLng(33.382576, -117.589417),
        new google.maps.LatLng(33.385801, -117.595167),
        new google.maps.LatLng(33.434951, -117.637739),
        new google.maps.LatLng(33.459874, -117.715244), // Dana Point Marine Life Refuge
        new google.maps.LatLng(33.553842, -117.819872),
        new google.maps.LatLng(33.606149, -117.928448),
        new google.maps.LatLng(33.686139, -118.039427),
        new google.maps.LatLng(33.729908, -118.090668),
        new google.maps.LatLng(33.723108, -118.136909),
        new google.maps.LatLng(33.723108, -118.206303),
        new google.maps.LatLng(33.7037, -118.267522),
        new google.maps.LatLng(33.705313, -118.28855),
        new google.maps.LatLng(33.704706, -118.294516),
        new google.maps.LatLng(33.713774, -118.317604), // white point state park
        new google.maps.LatLng(33.727266, -118.35155),
        new google.maps.LatLng(33.736332, -118.369617),
        new google.maps.LatLng(33.736225, -118.398242),
        new google.maps.LatLng(33.741007, -118.411288),
        new google.maps.LatLng(33.751712, -118.414721),
        new google.maps.LatLng(33.763379, -118.42236),
        new google.maps.LatLng(33.766198, -118.423948),
        new google.maps.LatLng(33.773868, -118.428369),
        new google.maps.LatLng(33.782679, -118.422704),
        new google.maps.LatLng(33.788207, -118.414936),
        new google.maps.LatLng(33.796731, -118.408713),
        new google.maps.LatLng(33.800262, -118.404593),
        new google.maps.LatLng(33.802081, -118.400087),
        new google.maps.LatLng(33.815595, -118.391204),
        new google.maps.LatLng(33.834633, -118.390517),
        new google.maps.LatLng(33.849924, -118.400817),
        new google.maps.LatLng(33.855449, -118.400259),
        new google.maps.LatLng(33.867351, -118.404508),
        new google.maps.LatLng(33.894429, -118.418412),
        new google.maps.LatLng(33.925165, -118.434591),
        new google.maps.LatLng(33.950124, -118.448668),
        new google.maps.LatLng(33.960162, -118.455491),
        new google.maps.LatLng(33.964647, -118.459547),
        new google.maps.LatLng(33.975147, -118.465576),
        new google.maps.LatLng(33.983563, -118.472915),
        new google.maps.LatLng(33.985467, -118.476455), //Venice Beach Park
        new google.maps.LatLng(33.990947, -118.479481),
        new google.maps.LatLng(34.003364, -118.490596),
        new google.maps.LatLng(34.011831, -118.501496),
        new google.maps.LatLng(34.025028, -118.517118),
        new google.maps.LatLng(34.027588, -118.521237),
        new google.maps.LatLng(34.032425, -118.530936),
        new google.maps.LatLng(34.036373, -118.540034),
        new google.maps.LatLng(34.037973, -118.54321),
        new google.maps.LatLng(34.038934, -118.54836),
        new google.maps.LatLng(34.037831, -118.554239),
        new google.maps.LatLng(34.037831, -118.555999),
        new google.maps.LatLng(34.041423, -118.56926),
        new google.maps.LatLng(34.039396, -118.576469),
        new google.maps.LatLng(34.037475, -118.58398),
        new google.maps.LatLng(34.039289, -118.593807),
        new google.maps.LatLng(34.038934, -118.600588),
        new google.maps.LatLng(34.03648, -118.609343),
        new google.maps.LatLng(34.0368, -118.615179),
        new google.maps.LatLng(34.036978, -118.626466),
        new google.maps.LatLng(34.036124, -118.636165),
        new google.maps.LatLng(34.037369, -118.649125),
        new google.maps.LatLng(34.038827, -118.668652),
        new google.maps.LatLng(34.036764, -118.676677),
        new google.maps.LatLng(34.034097, -118.67835),
        new google.maps.LatLng(34.032354, -118.67938),
        new google.maps.LatLng(34.03111, -118.68114),
        new google.maps.LatLng(34.030683, -118.683157),
        new google.maps.LatLng(34.032283, -118.695774),
        new google.maps.LatLng(34.029616, -118.705816),
        new google.maps.LatLng(34.029509, -118.70676),
        new google.maps.LatLng(34.031038, -118.719077),
        new google.maps.LatLng(34.031679, -118.723111),
        new google.maps.LatLng(34.032603, -118.739204),
        new google.maps.LatLng(34.031358, -118.74783),
        new google.maps.LatLng(34.025703, -118.756242),
        new google.maps.LatLng(34.024174, -118.77079),
        new google.maps.LatLng(34.021506, -118.783665),
        new google.maps.LatLng(34.011866, -118.792505),
        new google.maps.LatLng(34.008345, -118.793664),
        new google.maps.LatLng(34.006815, -118.794994),
        new google.maps.LatLng(34.005427, -118.80229),
        new google.maps.LatLng(34.000411, -118.805208),
        new google.maps.LatLng(34.000055, -118.806796),
        new google.maps.LatLng(34.001443, -118.808727),
        new google.maps.LatLng(34.026486, -118.83894),
        new google.maps.LatLng(34.039431, -118.89782),
        new google.maps.LatLng(34.041458, -118.915329),
        new google.maps.LatLng(34.045263, -118.945885),
        new google.maps.LatLng(34.051948, -118.964252),
        new google.maps.LatLng(34.069156, -119.012661),
        new google.maps.LatLng(34.085365, -119.061928),
        new google.maps.LatLng(34.096169, -119.082699),
        new google.maps.LatLng(34.094748, -119.110336),
        new google.maps.LatLng(34.100434, -119.131966),
        new google.maps.LatLng(34.130279, -119.176254),
        new google.maps.LatLng(34.14431, -119.210136),
        new google.maps.LatLng(34.145597, -119.216241),
        new google.maps.LatLng(34.15919, -119.229298),
        new google.maps.LatLng(34.186956, -119.243202),
        new google.maps.LatLng(34.394445, -119.726257), // Santa Barbara
        new google.maps.LatLng(34.406839, -119.877577),
        new google.maps.LatLng(34.468071, -120.113697),
        new google.maps.LatLng(34.468071, -120.238323),
        new google.maps.LatLng(34.466655, -120.278149),
        new google.maps.LatLng(34.450237, -120.419941),
        new google.maps.LatLng(34.442168, -120.452385),
        new google.maps.LatLng(34.448113, -120.471439),
        new google.maps.LatLng(34.46708, -120.474186),
        new google.maps.LatLng(34.495522, -120.496674),
        new google.maps.LatLng(34.516458, -120.50663),
        new google.maps.LatLng(34.541207, -120.553493),
        new google.maps.LatLng(34.556194, -120.585766),
        new google.maps.LatLng(34.553508, -120.623531),
        new google.maps.LatLng(34.56298, -120.638981),
        new google.maps.LatLng(34.576974, -120.650654), //Arguello
        new google.maps.LatLng(34.624168, -120.631342),
        new google.maps.LatLng(34.684817, -120.606108),
        new google.maps.LatLng(34.696743, -120.602245),
        new google.maps.LatLng(34.708669, -120.601773),
        new google.maps.LatLng(34.714595, -120.607395),
        new google.maps.LatLng(34.723167, -120.612073),
        new google.maps.LatLng(34.734877, -120.619197),
        new google.maps.LatLng(34.73791, -120.62602),
        new google.maps.LatLng(34.744434, -120.630355),
        new google.maps.LatLng(34.751028, -120.630956),
        new google.maps.LatLng(34.755999, -120.637693),
        new google.maps.LatLng(34.811619, -120.617952),
        new google.maps.LatLng(34.85875, -120.611515),
        new google.maps.LatLng(34.873679, -120.628166),
        new google.maps.LatLng(34.880439, -120.639324),
        new google.maps.LatLng(34.89283, -120.642672),
        new google.maps.LatLng(34.899237, -120.645075),
        new google.maps.LatLng(34.901771, -120.670652),
        new google.maps.LatLng(34.902967, -120.671682),
        new google.maps.LatLng(34.930134, -120.666704),
        new google.maps.LatLng(34.963482, -120.652971),
        new google.maps.LatLng(35.012143, -120.637951),
        new google.maps.LatLng(35.055224, -120.630827),
        new google.maps.LatLng(35.098844, -120.630569),
        new google.maps.LatLng(35.128403, -120.638208),
        new google.maps.LatLng(35.146021, -120.649796),
        new google.maps.LatLng(35.152618, -120.673656),
        new google.maps.LatLng(35.159846, -120.687046),
        new google.maps.LatLng(35.172826, -120.704041),
        new google.maps.LatLng(35.175071, -120.726013),
        new google.maps.LatLng(35.175562, -120.753136),
        new google.maps.LatLng(35.155776, -120.749531),
        new google.maps.LatLng(35.159775, -120.759573),
        new google.maps.LatLng(35.186155, -120.807724),
        new google.maps.LatLng(35.234403, -120.884628),
        new google.maps.LatLng(35.255432, -120.899391),
        new google.maps.LatLng(35.369735, -120.870037), //morro bay
        new google.maps.LatLng(35.434379, -120.887375),
        new google.maps.LatLng(35.46067, -121.005478),
        new google.maps.LatLng(35.663991, -121.284943),
        new google.maps.LatLng(35.885712, -121.461411),
        new google.maps.LatLng(36.020781, -121.571274),
        new google.maps.LatLng(36.232643, -121.812286),
        new google.maps.LatLng(36.302987, -121.899662),
        new google.maps.LatLng(36.307241, -121.902752),
        new google.maps.LatLng(36.354951, -121.907043),
        new google.maps.LatLng(36.581349, -121.977768),
        new google.maps.LatLng(36.636468, -121.935196),
        new google.maps.LatLng(36.786192, -121.79512),
        new google.maps.LatLng(36.950441, -121.881638),
        new google.maps.LatLng(36.949892, -122.027206), //Santa Cruz
        new google.maps.LatLng(36.975678, -122.153549),
        new google.maps.LatLng(37.089692, -122.275772),
        new google.maps.LatLng(37.112693, -122.330704),
        new google.maps.LatLng(37.118716, -122.338257),
        new google.maps.LatLng(37.148277, -122.358856),
        new google.maps.LatLng(37.195878, -122.405548),
        new google.maps.LatLng(37.245635, -122.419281),
        new google.maps.LatLng(37.439974, -122.444687),
        new google.maps.LatLng(37.483577, -122.453613),
        new google.maps.LatLng(37.496652, -122.500992),
        new google.maps.LatLng(37.534777, -122.520905),
        new google.maps.LatLng(37.594104, -122.518158),
        new google.maps.LatLng(37.782112, -122.512665),  //Sea Cliff
        new google.maps.LatLng(37.837988, -122.549744),
        new google.maps.LatLng(37.859134, -122.585449),
        new google.maps.LatLng(37.881899, -122.628021),
        new google.maps.LatLng(37.89653, -122.641068),
        new google.maps.LatLng(37.893279, -122.704926),
        new google.maps.LatLng(37.903574, -122.726212),
        new google.maps.LatLng(37.947446, -122.781143),
        new google.maps.LatLng(38.028352, -122.908173),
        new google.maps.LatLng(37.989804, -122.964478),
        new google.maps.LatLng(37.991834, -122.995205),
        new google.maps.LatLng(37.99508, -123.024044),
        new google.maps.LatLng(38.23818, -122.994003),
        new google.maps.LatLng(38.322266, -123.076401),
        new google.maps.LatLng(38.425084, -123.118286),
        new google.maps.LatLng(38.565616, -123.333206),
        new google.maps.LatLng(38.848264, -123.648376),
        new google.maps.LatLng(38.954069, -123.739014),
        new google.maps.LatLng(39.451571, -123.814545),
        new google.maps.LatLng(39.726729, -123.832054),
        new google.maps.LatLng(39.867588, -123.909302),
        new google.maps.LatLng(40.028666, -124.07959), //Shelter Cove Airport
        new google.maps.LatLng(40.261451, -124.363518),
        new google.maps.LatLng(40.440154, -124.409351),
        new google.maps.LatLng(40.652513, -124.308243),
        new google.maps.LatLng(40.713826, -124.263439),
        new google.maps.LatLng(40.791459, -124.211769),
        new google.maps.LatLng(40.922852, -124.138641),
        new google.maps.LatLng(41.046864, -124.125595),
        new google.maps.LatLng(41.051007, -124.150143),
        new google.maps.LatLng(41.053207, -124.153233),
        new google.maps.LatLng(41.072493, -124.159241),
        new google.maps.LatLng(41.101604, -124.162846),
        new google.maps.LatLng(41.141174, -124.161816), //Patricks Point State Park
        new google.maps.LatLng(41.235995, -124.108772), //Dry Lagoon State Park
        new google.maps.LatLng(41.451991, -124.0662),
        new google.maps.LatLng(41.509863, -124.081306),
        new google.maps.LatLng(41.524435, -124.084976),
        new google.maps.LatLng(41.53621, -124.082165),
        new google.maps.LatLng(41.545878, -124.082379),
        new google.maps.LatLng(41.548255, -124.083066),
        new google.maps.LatLng(41.551081, -124.088817),
        new google.maps.LatLng(41.553072, -124.092336),
        new google.maps.LatLng(41.560683, -124.096584),
        new google.maps.LatLng(41.563926, -124.098902),
        new google.maps.LatLng(41.569064, -124.101133),
        new google.maps.LatLng(41.571247, -124.101562),
        new google.maps.LatLng(41.574297, -124.102292),
        new google.maps.LatLng(41.579113, -124.102206),
        new google.maps.LatLng(41.579594, -124.101863),
        new google.maps.LatLng(41.583511, -124.098601),
        new google.maps.LatLng(41.588935, -124.101348),
        new google.maps.LatLng(41.591054, -124.102378),
        new google.maps.LatLng(41.594295, -124.104867),
        new google.maps.LatLng(41.594777, -124.104695),
        new google.maps.LatLng(41.600281, -124.100833),
        new google.maps.LatLng(41.605872, -124.104728),
        new google.maps.LatLng(41.606739, -124.105403),
        new google.maps.LatLng(41.611432, -124.109159),
        new google.maps.LatLng(41.614127, -124.111401),
        new google.maps.LatLng(41.614961, -124.11581),
        new google.maps.LatLng(41.61525, -124.115896),
        new google.maps.LatLng(41.621089, -124.11551),
        new google.maps.LatLng(41.621859, -124.115725),
        new google.maps.LatLng(41.622565, -124.11581),
        new google.maps.LatLng(41.628724, -124.115982),
        new google.maps.LatLng(41.630777, -124.116883),
        new google.maps.LatLng(41.632637, -124.118686),
        new google.maps.LatLng(41.634049, -124.119501),
        new google.maps.LatLng(41.639052, -124.11963),
        new google.maps.LatLng(41.642035, -124.121432),
        new google.maps.LatLng(41.643382, -124.122934),
        new google.maps.LatLng(41.645562, -124.124007),
        new google.maps.LatLng(41.646396, -124.125423),
        new google.maps.LatLng(41.648641, -124.126797),
        new google.maps.LatLng(41.651335, -124.127827),
        new google.maps.LatLng(41.652906, -124.130015),
        new google.maps.LatLng(41.656433, -124.134521),
        new google.maps.LatLng(41.657427, -124.135466),
        new google.maps.LatLng(41.666917, -124.13847),
        new google.maps.LatLng(41.668616, -124.138513),
        new google.maps.LatLng(41.671245, -124.138598),
        new google.maps.LatLng(41.672078, -124.140916),
        new google.maps.LatLng(41.672719, -124.141173),
        new google.maps.LatLng(41.675188, -124.139371),
        new google.maps.LatLng(41.676983, -124.138298),
        new google.maps.LatLng(41.680509, -124.138598),
        new google.maps.LatLng(41.681502, -124.139585),
        new google.maps.LatLng(41.682784, -124.14083),
        new google.maps.LatLng(41.687912, -124.143534),
        new google.maps.LatLng(41.691149, -124.143233),
        new google.maps.LatLng(41.696436, -124.143877),
        new google.maps.LatLng(41.696661, -124.143491),
        new google.maps.LatLng(41.707074, -124.144778),
        new google.maps.LatLng(41.713033, -124.145207),
        new google.maps.LatLng(41.716845, -124.147053),
        new google.maps.LatLng(41.724693, -124.151816),
        new google.maps.LatLng(41.740354, -124.165421),
        new google.maps.LatLng(41.743684, -124.171),
        new google.maps.LatLng(41.745221, -124.176621),
        new google.maps.LatLng(41.745765, -124.177952),
        new google.maps.LatLng(41.739585, -124.183574),
        new google.maps.LatLng(41.736159, -124.190998),
        new google.maps.LatLng(41.736735, -124.194775),
        new google.maps.LatLng(41.745509, -124.200697),
        new google.maps.LatLng(41.746149, -124.203186),
        new google.maps.LatLng(41.748263, -124.206362),
        new google.maps.LatLng(41.749928, -124.212027),
        new google.maps.LatLng(41.750632, -124.215202),
        new google.maps.LatLng(41.750696, -124.216146),
        new google.maps.LatLng(41.7514, -124.216404),
        new google.maps.LatLng(41.751849, -124.215031),
        new google.maps.LatLng(41.753706, -124.215374),
        new google.maps.LatLng(41.755498, -124.22061),
        new google.maps.LatLng(41.756459, -124.222069),
        new google.maps.LatLng(41.757099, -124.222155),
        new google.maps.LatLng(41.762605, -124.227648),
        new google.maps.LatLng(41.767407, -124.235973),
        new google.maps.LatLng(41.77208, -124.242496),
        new google.maps.LatLng(41.770415, -124.246616),
        new google.maps.LatLng(41.7708, -124.247732),
        new google.maps.LatLng(41.771632, -124.249449),
        new google.maps.LatLng(41.770095, -124.252796),
        new google.maps.LatLng(41.770864, -124.254255),
        new google.maps.LatLng(41.776817, -124.251165),
        new google.maps.LatLng(41.778353, -124.254684),
        new google.maps.LatLng(41.781553, -124.255199),
        new google.maps.LatLng(41.782961, -124.2558),
        new google.maps.LatLng(41.785201, -124.254341),
        new google.maps.LatLng(41.813291, -124.233055),
        new google.maps.LatLng(41.83702, -124.22267),
        new google.maps.LatLng(41.884515, -124.209795),
        new google.maps.LatLng(41.894611, -124.207478),
        new google.maps.LatLng(41.904257, -124.206619),
        new google.maps.LatLng(41.912625, -124.205675),
        new google.maps.LatLng(41.920098, -124.204817),
        new google.maps.LatLng(41.927633, -124.204903),
        new google.maps.LatLng(41.932231, -124.204731),
        new google.maps.LatLng(41.937403, -124.203701),
        new google.maps.LatLng(41.945447, -124.204903),
        new google.maps.LatLng(41.94583, -124.207478),
        new google.maps.LatLng(41.94583, -124.216404),
        new google.maps.LatLng(41.951065, -124.217091),
        new google.maps.LatLng(41.971743, -124.205246),
        new google.maps.LatLng(41.989226, -124.208851),
        new google.maps.LatLng(41.998284, -124.212112) //top left
    ];

    //Colorado
    else if((state == 'CO')||(state=='Colorado'))
    stateBoundaries = [  
        new google.maps.LatLng(36.999,-109.044), //bottom left
        new google.maps.LatLng(37 ,-103.001),
        new google.maps.LatLng(36.993,-102.041), //bottom right
        new google.maps.LatLng(41.002,-102.051), //top right
        new google.maps.LatLng(41,-109.049)      //top left
    ];

    //Connecticut ( TODO: Need better MA border )
    else if((state == 'CT')||(state=='Connecticut'))
    stateBoundaries = [  
        new google.maps.LatLng(41.423 ,-71.798), // bottom right
        new google.maps.LatLng(42.008 ,-71.8),   // top of RI section
        new google.maps.LatLng(42.023 ,-71.8),   // top right
        new google.maps.LatLng(42.049 ,-73.488), // top left
        new google.maps.LatLng(41.295 ,-73.551), 
        new google.maps.LatLng(41.212 ,-73.482), 
        new google.maps.LatLng(41.101 ,-73.728), 
        new google.maps.LatLng(40.991 ,-73.659)  // bottom left
    ];

    //Delaware
    else if((state == 'DE')||(state=='Delaware'))
    stateBoundaries = [  
        new google.maps.LatLng(38.78781,-75.012589), //bottom of NJ border
        new google.maps.LatLng(39.057584,-75.168457),
        new google.maps.LatLng(39.076776,-75.18837),
        new google.maps.LatLng(39.250334,-75.319519),
        new google.maps.LatLng(39.384202,-75.478134),
        new google.maps.LatLng(39.455282,-75.559845),
        new google.maps.LatLng(39.496093,-75.559158),
        new google.maps.LatLng(39.497153,-75.528259),
        new google.maps.LatLng(39.541117,-75.532379),
        new google.maps.LatLng(39.563353,-75.513153),
        new google.maps.LatLng(39.606217,-75.557098),
        new google.maps.LatLng(39.630019,-75.559845),
        new google.maps.LatLng(39.697149,-75.506973),
        new google.maps.LatLng(39.763158,-75.461655),
        new google.maps.LatLng(39.777408,-75.448608),
        new google.maps.LatLng(39.796403,-75.404663),
        new google.maps.LatLng(39.802 ,-75.415),     //bottom of PA border
        new google.maps.LatLng(39.795876,-75.405006),//top right
        new google.maps.LatLng(39.802 ,-75.415),     
        new google.maps.LatLng(39.820667,-75.454102),
        new google.maps.LatLng(39.837805,-75.526886),
        new google.maps.LatLng(39.83675,-75.59967),
        new google.maps.LatLng(39.82304,-75.657692),
        new google.maps.LatLng(39.807 ,-75.693),
        new google.maps.LatLng(39.788 ,-75.721),
        new google.maps.LatLng(39.721 ,-75.789),     //top left
        new google.maps.LatLng(38.46 ,-75.693),
        new google.maps.LatLng(38.451 ,-75.093),
        new google.maps.LatLng(38.451 ,-75.058),
        new google.maps.LatLng(38.451 ,-75.049)      //bottom right
    ];

    //Florida
    else if((state == 'FL')||(state=='Florida'))
    stateBoundaries = [  
        new google.maps.LatLng(30.727 ,-81.494),    //top right
        new google.maps.LatLng(30.791 ,-82.016),
        new google.maps.LatLng(30.374 ,-82.037),
        new google.maps.LatLng(30.358 ,-82.16),
        new google.maps.LatLng(30.568 ,-82.215),
        new google.maps.LatLng(30.711 ,-84.864),   
        new google.maps.LatLng(31, -85.002),        //left side of GA border
        new google.maps.LatLng(30.997 ,-87.598),    //top left
        new google.maps.LatLng(30.846 ,-87.625 ),
        new google.maps.LatLng(30.655 ,-87.397 ),
        new google.maps.LatLng(30.466 ,-87.426),    //bottom left of panhandle
        
        //Coastline
        new google.maps.LatLng(30.447194,-87.370834),
        new google.maps.LatLng(30.441422,-87.367229),
        new google.maps.LatLng(30.435354,-87.367229),
        new google.maps.LatLng(30.431062,-87.370491),
        new google.maps.LatLng(30.41774,-87.394695),
        new google.maps.LatLng(30.409449,-87.404652),
        new google.maps.LatLng(30.410042,-87.424049),
        new google.maps.LatLng(30.397753,-87.434006),
        new google.maps.LatLng(30.390498,-87.441044),
        new google.maps.LatLng(30.382057,-87.438641),
        new google.maps.LatLng(30.368284,-87.450485),
        new google.maps.LatLng(30.346361,-87.450829),
        new google.maps.LatLng(30.334065,-87.462845),
        new google.maps.LatLng(30.328138,-87.501469),
        new google.maps.LatLng(30.323545,-87.504902),
        new google.maps.LatLng(30.309171,-87.50473),
        new google.maps.LatLng(30.304873,-87.491169),
        new google.maps.LatLng(30.310949,-87.450314),
        new google.maps.LatLng(30.300427,-87.452545),
        new google.maps.LatLng(30.287976,-87.500095),
        new google.maps.LatLng(30.283974,-87.518291),
        new google.maps.LatLng(30.278786,-87.51812),
        new google.maps.LatLng(30.297611,-87.419243),    //Gulf Islands National Seashore
        new google.maps.LatLng(30.319248,-87.313843),
        new google.maps.LatLng(30.315543,-87.268696),
        new google.maps.LatLng(30.320878,-87.204494),
        new google.maps.LatLng(30.354065,-87.012749),
        new google.maps.LatLng(30.378947,-86.861),
        new google.maps.LatLng(30.389757,-86.765556),
        new google.maps.LatLng(30.395088,-86.676464),
        new google.maps.LatLng(30.392867,-86.594582),
        new google.maps.LatLng(30.380724,-86.510811),
        new google.maps.LatLng(30.38028,-86.504631),
        new google.maps.LatLng(30.382797,-86.454506),
        new google.maps.LatLng(30.375393,-86.377258),
        new google.maps.LatLng(30.36621,-86.317005),
        new google.maps.LatLng(30.329324,-86.174355),
        new google.maps.LatLng(30.29598,-86.070671),
        new google.maps.LatLng(30.270484,-86.00029),
        new google.maps.LatLng(30.235934,-85.919609),
        new google.maps.LatLng(30.166946,-85.792923),    //Panama City Beach
        new google.maps.LatLng(30.136517,-85.750179),
        new google.maps.LatLng(30.12167,-85.733185),
        new google.maps.LatLng(30.117809,-85.729408),
        new google.maps.LatLng(30.093603,-85.690956),
        new google.maps.LatLng(30.052008,-85.594139),
        new google.maps.LatLng(30.033433,-85.578346),
        new google.maps.LatLng(30.008166,-85.551567),
        new google.maps.LatLng(29.995678,-85.539894),
        new google.maps.LatLng(29.978729,-85.519123),
        new google.maps.LatLng(29.960884,-85.489597),
        new google.maps.LatLng(29.957165,-85.47226),
        new google.maps.LatLng(29.958207,-85.461617),
        new google.maps.LatLng(29.949878,-85.430717),
        new google.maps.LatLng(29.942887,-85.412521),
        new google.maps.LatLng(29.893341,-85.36068),
        new google.maps.LatLng(29.876672,-85.390205),
        new google.maps.LatLng(29.866549,-85.406942),
        new google.maps.LatLng(29.859925,-85.411835),
        new google.maps.LatLng(29.83506,-85.4181),
        new google.maps.LatLng(29.816668,-85.416727),
        new google.maps.LatLng(29.785833,-85.411062),
        new google.maps.LatLng(29.759385,-85.403938),
        new google.maps.LatLng(29.731067,-85.394068),
        new google.maps.LatLng(29.707139,-85.384111),
        new google.maps.LatLng(29.682535,-85.370035),
        new google.maps.LatLng(29.660833,-85.353041),
        new google.maps.LatLng(29.656283,-85.346861),
        new google.maps.LatLng(29.657253,-85.345058),
        new google.maps.LatLng(29.66844,-85.346947),
        new google.maps.LatLng(29.683728,-85.302057),
        new google.maps.LatLng(29.682237,-85.26721),
        new google.maps.LatLng(29.676346,-85.242405),
        new google.maps.LatLng(29.675973,-85.21966),
        new google.maps.LatLng(29.653822,-85.168505),
        new google.maps.LatLng(29.637411,-85.146189),
        new google.maps.LatLng(29.630473,-85.132713),
        new google.maps.LatLng(29.628235,-85.123272),
        new google.maps.LatLng(29.623609,-85.098124),    
        new google.maps.LatLng(29.585699,-85.051003),    //(less detail after this point)
        new google.maps.LatLng(29.628981,-84.91024),
        new google.maps.LatLng(29.897657,-84.345989),
        new google.maps.LatLng(29.900782,-84.341354),
        new google.maps.LatLng(29.923845,-84.331741),
        new google.maps.LatLng(29.971294,-84.330368),
        new google.maps.LatLng(30.070282,-84.166946),
        new google.maps.LatLng(30.067905,-83.997345),    //big cove
        new google.maps.LatLng(29.970104,-83.83049),
        new google.maps.LatLng(29.757821,-83.586044),
        new google.maps.LatLng(29.673735,-83.455582),
        new google.maps.LatLng(29.501769,-83.404083),
        new google.maps.LatLng(29.428235,-83.2901),
        new google.maps.LatLng(29.288196,-83.169937),
        new google.maps.LatLng(29.090377,-83.082733),
        new google.maps.LatLng(28.940261,-82.779236),
        new google.maps.LatLng(28.7207,-82.746964),	    //homosassa Bay
        new google.maps.LatLng(28.572462,-82.665253),
        new google.maps.LatLng(28.401065,-82.718811),
        new google.maps.LatLng(28.21487,-82.86026),
        new google.maps.LatLng(27.84879,-82.86026),	    //Indian Shores
        new google.maps.LatLng(27.527758,-82.757263),
        new google.maps.LatLng(27.046895,-82.451019),
        new google.maps.LatLng(26.784847,-82.279358),
        new google.maps.LatLng(26.667096,-82.271118),
        new google.maps.LatLng(26.464426,-82.184601),
        new google.maps.LatLng(26.431228,-82.147522),
        new google.maps.LatLng(26.41524,-82.081604),
        new google.maps.LatLng(26.436147,-81.959381),	//Fort Myers Beach
        new google.maps.LatLng(26.256473,-81.839905),
        new google.maps.LatLng(26.093788,-81.815186),
        new google.maps.LatLng(25.839449,-81.698456),
        new google.maps.LatLng(25.688563,-81.363373),
        new google.maps.LatLng(25.226063,-81.186218),
        new google.maps.LatLng(25.147771,-81.151886),
        new google.maps.LatLng(25.10301,-81.090088),
        new google.maps.LatLng(25.010951,-80.776978),
        new google.maps.LatLng(25.060721,-80.419922),    //Key Largo
        new google.maps.LatLng(25.438314,-80.128784),
        new google.maps.LatLng(25.815963,-80.106812),	//Miami Beach
        new google.maps.LatLng(26.832649,-80.005188), 	//North Palm Beach
        new google.maps.LatLng(27.927687,-80.399323),
        new google.maps.LatLng(28.455411,-80.500946),
        new google.maps.LatLng(28.591757,-80.550385),
        new google.maps.LatLng(29.602118,-81.12854),
        new google.maps.LatLng(30.168876,-81.318054),
        new google.maps.LatLng(30.704058,-81.400452)
    ];

    //Georgia
    else if((state == 'GA')||(state=='Georgia'))
    stateBoundaries = [  
        new google.maps.LatLng(32.032964,-80.837059), //coast ( bottom tip of SC )
        new google.maps.LatLng(32.099299,-81.081333), //savannah area
        new google.maps.LatLng(32.226 ,-81.144),
        new google.maps.LatLng(32.464 ,-81.186),
        new google.maps.LatLng(32.628 ,-81.418),
        new google.maps.LatLng(33.01 ,-81.495),
        new google.maps.LatLng(33.147 ,-81.751),
        new google.maps.LatLng(33.347 ,-81.94),
        new google.maps.LatLng(33.465 ,-81.929),
        new google.maps.LatLng(33.945 ,-82.557),
        new google.maps.LatLng(34.474 ,-82.877),
        new google.maps.LatLng(34.682 ,-83.342),
        new google.maps.LatLng(35 ,-83.108),          //top right     
        new google.maps.LatLng(34.988 ,-84.322),      //left side of NC segment
        new google.maps.LatLng(34.984 ,-85.605),      //top left
        new google.maps.LatLng(32.864 ,-85.184 ),   
        new google.maps.LatLng(32.426 ,-84.964 ), 
        new google.maps.LatLng(32.326 ,-85.006 ), 
        new google.maps.LatLng(32.261 ,-84.889 ), 
        new google.maps.LatLng(32.133 ,-85.062 ),  
        new google.maps.LatLng(31.84 , -85.141 ),
        new google.maps.LatLng(31.538 ,-85.041 ), 
        new google.maps.LatLng(31.188 ,-85.108 ),
        new google.maps.LatLng(31, -85.002),          //bottom of AL border
        new google.maps.LatLng(31, -85.002),          //bottom left
        new google.maps.LatLng(30.711 ,-84.864),   
        new google.maps.LatLng(30.568 ,-82.215),
        new google.maps.LatLng(30.358 ,-82.16),
        new google.maps.LatLng(30.374 ,-82.037),
        new google.maps.LatLng(30.791 ,-82.016),
        new google.maps.LatLng(30.727 ,-81.494)       //bottom right
    ];

	//HI
    else if((state == 'HI')||(state=='Hawaii'))
    stateBoundaries = [  
		new google.maps.LatLng(18.921786345087,-155.666192321268),
		new google.maps.LatLng(19.0422106324423,-155.537729780537),
		new google.maps.LatLng(19.1224845466533,-155.521678713445),
		new google.maps.LatLng(19.234878398743,-155.304899038619),
		new google.maps.LatLng(19.2669919638143,-155.152372136648),
		new google.maps.LatLng(19.3312115354762,-155.015881706841),
		new google.maps.LatLng(19.4195186485267,-154.927581824535),
		new google.maps.LatLng(19.4596764207728,-154.815187460665),
		new google.maps.LatLng(19.5399604106849,-154.791096018678),
		new google.maps.LatLng(19.6523353631656,-154.975762475196),
		new google.maps.LatLng(19.7406434419399,-154.975763078096),
		new google.maps.LatLng(19.7245855257612,-155.072105964772),
		new google.maps.LatLng(19.8771146284228,-155.096185006144),
		new google.maps.LatLng(19.9975288183811,-155.200550212445),
		new google.maps.LatLng(20.134006968001,-155.561825953369),
		new google.maps.LatLng(20.2062567699811,-155.73844208468),
		new google.maps.LatLng(20.2704795241864,-155.858857946276),
		new google.maps.LatLng(20.1741490505645,-155.8909788308),
		new google.maps.LatLng(20.0617574252623,-155.834786659291),
		new google.maps.LatLng(20.0055628964617,-155.826761720261),
		new google.maps.LatLng(19.9333119780873,-155.882958698574),
		new google.maps.LatLng(19.8209225278706,-155.979300942813),
		new google.maps.LatLng(19.7807832517673,-156.043524655521),
		new google.maps.LatLng(19.6282527113542,-155.971258610598),
		new google.maps.LatLng(19.4837502062815,-155.947178326912),
		new google.maps.LatLng(19.3713586083673,-155.874927042962),
		new google.maps.LatLng(19.1305132974745,-155.907031019716),
		new google.maps.LatLng(19.0101009103203,-155.858861500485),
		new google.maps.LatLng(19.01009268801,-155.778585834471),
		new google.maps.LatLng(18.921786345087,-155.666192321268)
	];
	else if((state == 'HI2')||(state=='Hawaii2'))
    stateBoundaries = [  
		new google.maps.LatLng(21.9483433404175,-159.335174733889),
		new google.maps.LatLng(22.0446395507162,-159.327130348878),
		new google.maps.LatLng(22.1248124949548,-159.295025589769),
		new google.maps.LatLng(22.1970166285359,-159.343195828355),
		new google.maps.LatLng(22.2291198667724,-159.391366885913),
		new google.maps.LatLng(22.2131796383001,-159.576012589057),
		new google.maps.LatLng(22.1490592515515,-159.712505933171),
		new google.maps.LatLng(22.0366665967853,-159.800814224332),
		new google.maps.LatLng(21.9644203111023,-159.736592652746),
		new google.maps.LatLng(21.9483657695954,-159.640246973766),
		new google.maps.LatLng(21.8841361312636,-159.576021285803),
		new google.maps.LatLng(21.8680716835921,-159.439545188912),
		new google.maps.LatLng(21.9483433404175,-159.335174733889)
	];
	else if((state == 'HI3')||(state=='Hawaii3'))
    stateBoundaries = [  
		new google.maps.LatLng(22.0041773078075,-160.073803556017),
		new google.maps.LatLng(21.9639787234984,-160.121962433575),
		new google.maps.LatLng(21.8915919620539,-160.22633646805),
		new google.maps.LatLng(21.8032804408925,-160.242406159206),
		new google.maps.LatLng(21.7953086498352,-160.202259673133),
		new google.maps.LatLng(21.8675963686431,-160.170137686564),
		new google.maps.LatLng(21.9158698594272,-160.089858388217),
		new google.maps.LatLng(21.9881641068501,-160.04970969565),
		new google.maps.LatLng(22.0041773078075,-160.073803556017)
	];
	else if((state == 'HI4')||(state=='Hawaii4'))
    stateBoundaries = [  
		new google.maps.LatLng(21.2980271804006,-157.673329876454),
		new google.maps.LatLng(21.3863351682207,-157.713472767801),
		new google.maps.LatLng(21.4585875674916,-157.761642948927),
		new google.maps.LatLng(21.4345051589313,-157.809815119729),
		new google.maps.LatLng(21.5067588029631,-157.849959026307),
		new google.maps.LatLng(21.6352084806278,-157.914179741199),
		new google.maps.LatLng(21.6994328594403,-157.986428112199),
		new google.maps.LatLng(21.6753505680564,-158.042626247904),
		new google.maps.LatLng(21.5790166217416,-158.114894071646),
		new google.maps.LatLng(21.5870425200507,-158.267431808754),
		new google.maps.LatLng(21.5388789757719,-158.243342775004),
		new google.maps.LatLng(21.4746525812457,-158.235318726083),
		new google.maps.LatLng(21.3542322862678,-158.130951115411),
		new google.maps.LatLng(21.2900079773187,-158.098833012334),
		new google.maps.LatLng(21.3060610107082,-157.946306402713),
		new google.maps.LatLng(21.3301442923012,-157.898134535461),
		new google.maps.LatLng(21.249863668784,-157.825871692713),
		new google.maps.LatLng(21.2819711706226,-157.721501646872),
		new google.maps.LatLng(21.2739428101649,-157.681371117927),
		new google.maps.LatLng(21.2980271804006,-157.673329876454)
	];
	else if((state == 'HI5')||(state=='Hawaii5'))
    stateBoundaries = [
		new google.maps.LatLng(21.1374197607644,-156.717872676999),
		new google.maps.LatLng(21.1615268671346,-156.942682512532),
		new google.maps.LatLng(21.2096935134305,-156.958733487556),
		new google.maps.LatLng(21.1856106379825,-157.006903742467),
		new google.maps.LatLng(21.1936404563437,-157.167471487889),
		new google.maps.LatLng(21.2337763231398,-157.23169415835),
		new google.maps.LatLng(21.1615304515084,-157.247747506123),
		new google.maps.LatLng(21.1374484326638,-157.303948796958),
		new google.maps.LatLng(21.0812505718402,-157.287897522787),
		new google.maps.LatLng(21.1053309568586,-157.071131928318),
		new google.maps.LatLng(21.049134117125,-156.886484099806),
		new google.maps.LatLng(21.0651768177371,-156.766048197649),
		new google.maps.LatLng(21.1374197607644,-156.717872676999)
	];
	else if((state == 'HI6')||(state=='Hawaii6'))
    stateBoundaries = [  
		new google.maps.LatLng(20.6316494073652,-156.196045412482),
		new google.maps.LatLng(20.6236229186799,-156.131808903973),
		new google.maps.LatLng(20.6557325999668,-156.043511542555),
		new google.maps.LatLng(20.7520616316876,-155.98731705257),
		new google.maps.LatLng(20.8002253216075,-156.011392452548),
		new google.maps.LatLng(20.9286712687479,-156.260263363072),
		new google.maps.LatLng(20.9447263610079,-156.356603503292),
		new google.maps.LatLng(20.8965651391117,-156.477022052544),
		new google.maps.LatLng(20.9848701637724,-156.525191645863),
		new google.maps.LatLng(21.0410647054158,-156.597439619788),
		new google.maps.LatLng(21.0169850495629,-156.653636007318),
		new google.maps.LatLng(20.9126240100612,-156.693782634023),
		new google.maps.LatLng(20.8082609120439,-156.637586604165),
		new google.maps.LatLng(20.7761496574975,-156.525193571704),
		new google.maps.LatLng(20.7922042815103,-156.493074740185),
		new google.maps.LatLng(20.7279810872564,-156.460971893198),
		new google.maps.LatLng(20.6236212173367,-156.436879230878),
		new google.maps.LatLng(20.567426981473,-156.396734942946),
		new google.maps.LatLng(20.5834838609152,-156.276317407393),
		new google.maps.LatLng(20.6316494073652,-156.196045412482)
	];
	else if((state == 'HI7')||(state=='Hawaii7'))
    stateBoundaries = [  
		new google.maps.LatLng(20.928706972385,-157.039050670938),
		new google.maps.LatLng(20.880538425907,-157.055098211979),
		new google.maps.LatLng(20.8323776268078,-156.982826322974),
		new google.maps.LatLng(20.7922371514621,-156.990863465776),
		new google.maps.LatLng(20.7280207316388,-156.96676559866),
		new google.maps.LatLng(20.7360494091678,-156.886487178396),
		new google.maps.LatLng(20.7922527172797,-156.814225697816),
		new google.maps.LatLng(20.8404186121726,-156.806205341549),
		new google.maps.LatLng(20.9287186176943,-156.910585845163),
		new google.maps.LatLng(20.928706972385,-157.039050670938)
	];
	
    //Idaho
    else if((state == 'ID')||(state=='Idaho'))
    stateBoundaries = [   
        new google.maps.LatLng(48.9991,-117.0323),   //top left
        new google.maps.LatLng(48.999 ,-117.031),
        new google.maps.LatLng(46.354 ,-117.06),
        new google.maps.LatLng(46.168 ,-116.921),
        new google.maps.LatLng(46.088 ,-116.981),
        new google.maps.LatLng(45.995 ,-116.914),    //top of OR border
        new google.maps.LatLng(45.824 ,-116.779),
        new google.maps.LatLng(45.752 ,-116.547),
        new google.maps.LatLng(45.603 ,-116.462),
        new google.maps.LatLng(45.025 ,-116.844),
        new google.maps.LatLng(44.881 ,-116.855),
        new google.maps.LatLng(44.727 ,-117.06),
        new google.maps.LatLng(44.393 ,-117.241),
        new google.maps.LatLng(44.273 ,-117.196),
        new google.maps.LatLng(44.17 ,-116.894),
        new google.maps.LatLng(43.83 ,-117.031),
        new google.maps.LatLng(42.002 ,-117.024),    //bottom left
        new google.maps.LatLng(41.994 ,-114.04),     //left of UT border
        new google.maps.LatLng(42.001 ,-111.045),    //bottom right
        new google.maps.LatLng(44.475 ,-111.052),    //bottom of MT border
        new google.maps.LatLng(44.755 ,-111.383),
        new google.maps.LatLng(44.643 ,-111.514),
        new google.maps.LatLng(44.541 ,-111.471),
        new google.maps.LatLng(44.569 ,-112.284),
        new google.maps.LatLng(44.45 ,-112.383),
        new google.maps.LatLng(44.485 ,-112.778),
        new google.maps.LatLng(44.359 ,-112.854),
        new google.maps.LatLng(44.453 ,-113.004),
        new google.maps.LatLng(44.774 ,-113.135),
        new google.maps.LatLng(44.864 ,-113.451),
        new google.maps.LatLng(45.058 ,-113.45),
        new google.maps.LatLng(45.26 ,-113.689),
        new google.maps.LatLng(45.603 ,-113.807),
        new google.maps.LatLng(45.696 ,-113.937),
        new google.maps.LatLng(45.459 ,-114.332),
        new google.maps.LatLng(45.558 ,-114.563),
        new google.maps.LatLng(45.704 ,-114.495),
        new google.maps.LatLng(45.777 ,-114.562),
        new google.maps.LatLng(45.885 ,-114.386),
        new google.maps.LatLng(46.147 ,-114.525),
        new google.maps.LatLng(46.172 ,-114.444),
        new google.maps.LatLng(46.647 ,-114.319),
        new google.maps.LatLng(46.7 ,-114.769),
        new google.maps.LatLng(47.253 ,-115.317),
        new google.maps.LatLng(47.427 ,-115.756),
        new google.maps.LatLng(47.48 ,-115.627),
        new google.maps.LatLng(47.55 ,-115.754),
        new google.maps.LatLng(47.696 ,-115.723),
        new google.maps.LatLng(47.978 ,-116.047),
        new google.maps.LatLng(49 ,-116.048)         //top right    
    ];

    //Illinois
    else if((state == 'IL')||(state=='Illinois'))
    stateBoundaries = [  
        new google.maps.LatLng(42.492 ,-87.802),     //top right of land border
        new google.maps.LatLng(42.495 ,-88.94),
        new google.maps.LatLng(42.508 ,-90.64),      //top left
        new google.maps.LatLng(42.35 ,-90.44),
        new google.maps.LatLng(42.336 ,-90.428),
        new google.maps.LatLng(42.288 ,-90.427),
        new google.maps.LatLng(42.281 ,-90.43),
        new google.maps.LatLng(42.112 ,-90.161),
        new google.maps.LatLng(41.808 ,-90.181),
        new google.maps.LatLng(41.522 ,-90.462),
        new google.maps.LatLng(41.412 ,-91.046),
        new google.maps.LatLng(41.242 ,-91.113),
        new google.maps.LatLng(41.1 ,-90.948),
        new google.maps.LatLng(40.953 ,-90.952),
        new google.maps.LatLng(40.67 ,-91.123),
        new google.maps.LatLng(40.557 ,-91.402),
        new google.maps.LatLng(40.378 ,-91.419),      //bottom of IA border
        new google.maps.LatLng(40.125 ,-91.51), 
        new google.maps.LatLng(39.728 ,-91.367), 
        new google.maps.LatLng(39.257 ,-90.731), 
        new google.maps.LatLng(38.888 ,-90.625), 
        new google.maps.LatLng(38.967 ,-90.451), 
        new google.maps.LatLng(38.844 ,-90.109), 
        new google.maps.LatLng(38.326 ,-90.373), 
        new google.maps.LatLng(38.212 ,-90.351), 
        new google.maps.LatLng(37.958 ,-89.925), 
        new google.maps.LatLng(37.88 ,-89.949), 
        new google.maps.LatLng(37.903 ,-89.839), 
        new google.maps.LatLng(37.693 ,-89.517), 
        new google.maps.LatLng(37.387 ,-89.421), 
        new google.maps.LatLng(37.261 ,-89.522), 
        new google.maps.LatLng(37.038 ,-89.376), 
        new google.maps.LatLng(36.989 ,-89.276), 
        new google.maps.LatLng(37.072 ,-89.254), 
        new google.maps.LatLng(36.982 ,-89.133),     //bottom of MO border
        new google.maps.LatLng(37.067 ,-89.173),
        new google.maps.LatLng(37.224 ,-89 ,17),
        new google.maps.LatLng(37.159 ,-88.761),
        new google.maps.LatLng(37.145 ,-88.735),
        new google.maps.LatLng(37.075 ,-88.459),
        new google.maps.LatLng(37.391 ,-88.474),
        new google.maps.LatLng(37.488 ,-88.065),
        new google.maps.LatLng(37.656 ,-88.16),
        new google.maps.LatLng(37.799 ,-88.027),     //top of KY border
        new google.maps.LatLng(37.901 ,-88.097),
        new google.maps.LatLng(37.891 ,-88.014),
        new google.maps.LatLng(38.046 ,-88.042),
        new google.maps.LatLng(38.162 ,-87.911),
        new google.maps.LatLng(38.256 ,-87.988),
        new google.maps.LatLng(38.28 ,-87.841),
        new google.maps.LatLng(38.739 ,-87.496),
        new google.maps.LatLng(38.952 ,-87.512),
        new google.maps.LatLng(39.136 ,-87.658),
        new google.maps.LatLng(39.348 ,-87.531),
        new google.maps.LatLng(41.712 ,-87.524)      //top of left part of IN border
    ];

    //Indiana
    else if((state == 'IN')||(state=='Indiana'))
    stateBoundaries = [  
        new google.maps.LatLng(41.696 ,-84.806),     //top of OH border
        new google.maps.LatLng(39.105 ,-84.819),     //bottom right
        new google.maps.LatLng(39.055 ,-84.897),
        new google.maps.LatLng(38.785 ,-84.813),
        new google.maps.LatLng(38.688 ,-85.171),
        new google.maps.LatLng(38.728 ,-85.435),
        new google.maps.LatLng(38.715 ,-85.447),
        new google.maps.LatLng(38.534 ,-85.421),
        new google.maps.LatLng(38.299 ,-85.677),
        new google.maps.LatLng(38.278 ,-85.826),
        new google.maps.LatLng(38.025 ,-85.923),
        new google.maps.LatLng(37.958 ,-86.039),
        new google.maps.LatLng(38.056 ,-86.266),
        new google.maps.LatLng(38.198 ,-86.362),
        new google.maps.LatLng(38.138 ,-86.324),
        new google.maps.LatLng(38.121 ,-86.46),
        new google.maps.LatLng(37.927 ,-86.509),
        new google.maps.LatLng(37.843 ,-86.637),
        new google.maps.LatLng(37.999 ,-86.816),
        new google.maps.LatLng(37.782 ,-87.111),
        new google.maps.LatLng(37.976 ,-87.589),
        new google.maps.LatLng(37.833 ,-87.613),
        new google.maps.LatLng(37.928 ,-87.893),
        new google.maps.LatLng(37.807 ,-87.907),
        new google.maps.LatLng(37.799 ,-88.027),     //bottom left
        new google.maps.LatLng(37.901 ,-88.097),
        new google.maps.LatLng(37.891 ,-88.014),
        new google.maps.LatLng(38.046 ,-88.042),
        new google.maps.LatLng(38.162 ,-87.911),
        new google.maps.LatLng(38.256 ,-87.988),
        new google.maps.LatLng(38.28 ,-87.841),
        new google.maps.LatLng(38.739 ,-87.496),
        new google.maps.LatLng(38.952 ,-87.512),
        new google.maps.LatLng(39.136 ,-87.658),
        new google.maps.LatLng(39.348 ,-87.531),
        new google.maps.LatLng(41.712 ,-87.524),     //top left
        new google.maps.LatLng(41.76 ,-86.825),      
        new google.maps.LatLng(41.76 ,-84.806)       //top right
    ];

    //Iowa
    else if((state == 'IA')||(state=='Iowa'))
    stateBoundaries = [  
        new google.maps.LatLng(43.5 ,-96.453),       //top left
        new google.maps.LatLng(43.5 ,-96.598),
        new google.maps.LatLng(43.385 ,-96.52),
        new google.maps.LatLng(43.269 ,-96.585),
        new google.maps.LatLng(43.12 ,-96.436),
        new google.maps.LatLng(42.737 ,-96.637),
        new google.maps.LatLng(42.489 ,-96.443),     //top of NE border
        new google.maps.LatLng(42.167 ,-96.347),
        new google.maps.LatLng(41.798 ,-96.066),
        new google.maps.LatLng(41.608 ,-96.116),
        new google.maps.LatLng(41.601 ,-96.113),
        new google.maps.LatLng(41.452 ,-95.919),
        new google.maps.LatLng(41.346 ,-95.957),
        new google.maps.LatLng(41.302 ,-95.872),
        new google.maps.LatLng(41.201 ,-95.927),
        new google.maps.LatLng(41.177 ,-95.841),
        new google.maps.LatLng(40.895 ,-95.809),
        new google.maps.LatLng(40.734 ,-95.888),
        new google.maps.LatLng(40.585 ,-95.765),     //bottom left
        new google.maps.LatLng(40.614 ,-91.728),
        new google.maps.LatLng(40.403 ,-91.505),
        new google.maps.LatLng(40.4 ,-91.5),
        new google.maps.LatLng(40.378 ,-91.419),      //bottom right
        new google.maps.LatLng(40.557 ,-91.402),
        new google.maps.LatLng(40.67 ,-91.123),
        new google.maps.LatLng(40.953 ,-90.952),
        new google.maps.LatLng(41.1 ,-90.948),
        new google.maps.LatLng(41.242 ,-91.113),
        new google.maps.LatLng(41.412 ,-91.046),
        new google.maps.LatLng(41.522 ,-90.462),
        new google.maps.LatLng(41.808 ,-90.181),
        new google.maps.LatLng(42.112 ,-90.161),
        new google.maps.LatLng(42.281 ,-90.43),
        new google.maps.LatLng(42.288 ,-90.427),
        new google.maps.LatLng(42.336 ,-90.428),
        new google.maps.LatLng(42.35 ,-90.44),
        new google.maps.LatLng(42.508 ,-90.64),      //bottom of WI border
        new google.maps.LatLng(42.634 ,-90.707),
        new google.maps.LatLng(42.751 ,-91.065),
        new google.maps.LatLng(43.125 ,-91.178),
        new google.maps.LatLng(43.252 ,-91.059),
        new google.maps.LatLng(43.258 ,-91.062),
        new google.maps.LatLng(43.353 ,-91.206),
        new google.maps.LatLng(43.501 ,-91.217)     //top right
    ];

    //Kansas
    else if((state == 'KS')||(state=='Kansas'))
    stateBoundaries = [  
        new google.maps.LatLng(40.003 ,-102.051),    //top left
        new google.maps.LatLng(40 ,-95.307),         //top right
        new google.maps.LatLng(39.874 ,-95.13),
        new google.maps.LatLng(39.887 ,-94.928),
        new google.maps.LatLng(39.744 ,-94.861),
        new google.maps.LatLng(39.741 ,-94.962),
        new google.maps.LatLng(39.542 ,-95.109),
        new google.maps.LatLng(39.386 ,-94.882),
        new google.maps.LatLng(39.213 ,-94.827),
        new google.maps.LatLng(39.148 ,-94.588),
        new google.maps.LatLng(36.998 ,-94.618),     //bottom right
        new google.maps.LatLng(36.993 ,-102.041)     //bottom left
    ];

    //Kentucky (TODO: Make south border better. )
    else if((state == 'KY')||(state=='Kentucky'))
    stateBoundaries = [     
        new google.maps.LatLng(39.105 ,-84.819),     //top of IN border
        new google.maps.LatLng(39.055 ,-84.897),
        new google.maps.LatLng(38.785 ,-84.813),
        new google.maps.LatLng(38.688 ,-85.171),
        new google.maps.LatLng(38.728 ,-85.435),
        new google.maps.LatLng(38.715 ,-85.447),
        new google.maps.LatLng(38.534 ,-85.421),
        new google.maps.LatLng(38.299 ,-85.677),
        new google.maps.LatLng(38.278 ,-85.826),
        new google.maps.LatLng(38.025 ,-85.923),
        new google.maps.LatLng(37.958 ,-86.039),
        new google.maps.LatLng(38.056 ,-86.266),
        new google.maps.LatLng(38.198 ,-86.362),
        new google.maps.LatLng(38.138 ,-86.324),
        new google.maps.LatLng(38.121 ,-86.46),
        new google.maps.LatLng(37.927 ,-86.509),
        new google.maps.LatLng(37.843 ,-86.637),
        new google.maps.LatLng(37.999 ,-86.816),
        new google.maps.LatLng(37.782 ,-87.111),
        new google.maps.LatLng(37.976 ,-87.589),
        new google.maps.LatLng(37.833 ,-87.613),
        new google.maps.LatLng(37.928 ,-87.893),
        new google.maps.LatLng(37.807 ,-87.907),
        new google.maps.LatLng(37.799 ,-88.027),      //top of IL border
        new google.maps.LatLng(37.656 ,-88.16),
        new google.maps.LatLng(37.488 ,-88.065),
        new google.maps.LatLng(37.391 ,-88.474),
        new google.maps.LatLng(37.075 ,-88.459),
        new google.maps.LatLng(37.145 ,-88.735),
        new google.maps.LatLng(37.159 ,-88.761),
        new google.maps.LatLng(37.224 ,-89 ,17),
        new google.maps.LatLng(37.067 ,-89.173),
        new google.maps.LatLng(36.982 ,-89.133),     //top of MO border
        new google.maps.LatLng(36.666 ,-89.159), 
        new google.maps.LatLng(36.567 ,-89.236), 
        new google.maps.LatLng(36.625 ,-89.366), 
        new google.maps.LatLng(36.499 ,-89.417), 
        new google.maps.LatLng(36.497 ,-89.485), 
        new google.maps.LatLng(36.497218,-89.484673),
        new google.maps.LatLng(36.582 ,-89.53),
        new google.maps.LatLng(36.498 ,-89.539),     //bottom left
        new google.maps.LatLng(36.497 ,-89.485), 
        new google.maps.LatLng(36.499 ,-89.417), 
        new google.maps.LatLng(36.497 ,-88.053), 
        new google.maps.LatLng(36.678 ,-88.07), 
        new google.maps.LatLng(36.601 ,-83.675),     //bottom of VA Border
        new google.maps.LatLng(36.745 ,-83.133),
        new google.maps.LatLng(36.852 ,-83.074),
        new google.maps.LatLng(36.897 ,-82.873),
        new google.maps.LatLng(37.121 ,-82.721),
        new google.maps.LatLng(37.538 ,-81.968),     //bottom WV Border
        new google.maps.LatLng(37.678 ,-82.304),
        new google.maps.LatLng(37.93 ,-82.5),
        new google.maps.LatLng(37.98 ,-82.464),
        new google.maps.LatLng(38.162 ,-82.643),
        new google.maps.LatLng(38.421 ,-82.594),     //right OH Border
        new google.maps.LatLng(38.59 ,-82.843),
        new google.maps.LatLng(38.755 ,-82.889),
        new google.maps.LatLng(38.596 ,-83.293),
        new google.maps.LatLng(38.703 ,-83.526),
        new google.maps.LatLng(38.626 ,-83.668),
        new google.maps.LatLng(38.761 ,-83.869),
        new google.maps.LatLng(38.808 ,-84.215),
        new google.maps.LatLng(39.119 ,-84.451),
        new google.maps.LatLng(39.105 ,-84.819)      //left OH Border
    ];

    //Louisiana
    else if((state == 'LA')||(state=='Louisiana'))
    stateBoundaries = [   
        new google.maps.LatLng(33.019 ,-94.042),     //top left
        new google.maps.LatLng(31.992 ,-94.041),
        new google.maps.LatLng(31.777 ,-93.825),
        new google.maps.LatLng(31.585 ,-93.834),
        new google.maps.LatLng(31.509 ,-93.712),
        new google.maps.LatLng(31.031 ,-93.508),
        new google.maps.LatLng(30.538 ,-93.739),
        new google.maps.LatLng(30.137 ,-93.691),
        new google.maps.LatLng(29.999 ,-93.769),     //bottom left
        new google.maps.LatLng(29.795368,-93.927612),
        new google.maps.LatLng(29.764377,-93.891907),
        new google.maps.LatLng(29.683281,-93.834229),
        new google.maps.LatLng(29.59376,-93.810883),
        new google.maps.LatLng(29.616446,-92.774048),
        new google.maps.LatLng(29.51611,-92.298889),
        new google.maps.LatLng(29.458731,-91.819611),
        new google.maps.LatLng(29.227692,-91.271667),
        new google.maps.LatLng(29.018949,-90.920105),
        new google.maps.LatLng(29.053769,-90.22522),
        new google.maps.LatLng(28.906004,-89.420471),
        new google.maps.LatLng(28.960089,-89.110107),
        new google.maps.LatLng(29.186936,-88.981018),
        new google.maps.LatLng(30.147502,-89.077148),
        new google.maps.LatLng(30.212202,-89.183578),
        new google.maps.LatLng(30.180154,-89.265289),
        new google.maps.LatLng(30.176593,-89.421844),
        new google.maps.LatLng(30.157002,-89.508362),
        new google.maps.LatLng(30.193805,-89.531021),
        new google.maps.LatLng(30.192618,-89.547501),
        new google.maps.LatLng(30.182528,-89.559174),
        new google.maps.LatLng(30.181341,-89.57222),
        new google.maps.LatLng(30.202114,-89.589386),
        new google.maps.LatLng(30.221695,-89.614105),
        new google.maps.LatLng(30.216 ,-89.604),     //bottom right
        new google.maps.LatLng(30.664 ,-89.848),
        new google.maps.LatLng(31.004 ,-89.733),
        new google.maps.LatLng(30.999 ,-91.637),
        new google.maps.LatLng(31.054 ,-91.559),
        new google.maps.LatLng(31.257 ,-91.653),
        new google.maps.LatLng(31.278 ,-91.513),
        new google.maps.LatLng(31.407 ,-91.577),
        new google.maps.LatLng(31.373 ,-91.472),
        new google.maps.LatLng(31.524 ,-91.521),
        new google.maps.LatLng(31.573 ,-91.406),
        new google.maps.LatLng(31.63 ,-91.515),
        new google.maps.LatLng(31.619 ,-91.403),
        new google.maps.LatLng(31.746 ,-91.369),
        new google.maps.LatLng(31.754 ,-91.262),
        new google.maps.LatLng(31.76 ,-91.363),
        new google.maps.LatLng(31.847 ,-91.341),
        new google.maps.LatLng(31.813 ,-91.254),
        new google.maps.LatLng(32.011 ,-91.08),
        new google.maps.LatLng(32.074 ,-91.156),
        new google.maps.LatLng(32.145 ,-91.004),
        new google.maps.LatLng(32.134 ,-91.167),
        new google.maps.LatLng(32.197 ,-91.163),
        new google.maps.LatLng(32.193 ,-90.994),
        new google.maps.LatLng(32.367 ,-90.877),
        new google.maps.LatLng(32.354 ,-90.994),
        new google.maps.LatLng(32.436 ,-90.967),
        new google.maps.LatLng(32.481 ,-91.115),
        new google.maps.LatLng(32.547 ,-91.095),
        new google.maps.LatLng(32.494 ,-90.987),
        new google.maps.LatLng(32.543 ,-91.072),
        new google.maps.LatLng(32.64 ,-91.013),
        new google.maps.LatLng(32.618 ,-91.152),
        new google.maps.LatLng(32.719 ,-91.053),
        new google.maps.LatLng(32.778 ,-91.163),
        new google.maps.LatLng(32.905 ,-91.063),
        new google.maps.LatLng(32.989 ,-91.105),
        new google.maps.LatLng(32.916 ,-91.209),
        new google.maps.LatLng(33.004 ,-91.166)      //top right
    ];

    //Maine
    else if((state == 'ME')||(state=='Maine'))
    stateBoundaries = [   
        new google.maps.LatLng(45.306286,-71.08429),     //top left
        new google.maps.LatLng(45.314495,-71.059227),
        new google.maps.LatLng(45.311115,-71.033821),
        new google.maps.LatLng(45.328979,-71.004982),
        new google.maps.LatLng(45.346596,-71.011162),
        new google.maps.LatLng(45.332116,-70.984039),
        new google.maps.LatLng(45.339357,-70.951424),
        new google.maps.LatLng(45.312563,-70.918808),
        new google.maps.LatLng(45.296626,-70.911598),
        new google.maps.LatLng(45.279235,-70.921555),
        new google.maps.LatLng(45.252172,-70.895119),
        new google.maps.LatLng(45.24347,-70.897179),
        new google.maps.LatLng(45.230173,-70.85804),
        new google.maps.LatLng(45.236459,-70.838127),
        new google.maps.LatLng(45.243953,-70.848427),
        new google.maps.LatLng(45.263772,-70.84877),
        new google.maps.LatLng(45.293728,-70.835037),
        new google.maps.LatLng(45.297834,-70.816498),
        new google.maps.LatLng(45.337909,-70.819588),
        new google.maps.LatLng(45.398932,-70.825768),
        new google.maps.LatLng(45.429299,-70.795212),
        new google.maps.LatLng(45.428335,-70.756073),
        new google.maps.LatLng(45.391217,-70.712128),
        new google.maps.LatLng(45.395316,-70.682945),
        new google.maps.LatLng(45.386877,-70.661316),
        new google.maps.LatLng(45.377714,-70.659599),
        new google.maps.LatLng(45.383019,-70.63488),
        new google.maps.LatLng(45.415081,-70.63488),
        new google.maps.LatLng(45.489742,-70.717964),
        new google.maps.LatLng(45.514046,-70.722771),
        new google.maps.LatLng(45.569111,-70.687065),
        new google.maps.LatLng(45.666846,-70.559349),
        new google.maps.LatLng(45.706419,-70.466652),
        new google.maps.LatLng(45.728712,-70.396614),
        new google.maps.LatLng(45.794818,-70.4179),
        new google.maps.LatLng(45.836454,-70.369148),
        new google.maps.LatLng(45.893115,-70.266151),
        new google.maps.LatLng(45.92417,-70.262718),	    //lac du portage
        new google.maps.LatLng(45.963084,-70.31559),
        new google.maps.LatLng(46.064417,-70.309753),
        new google.maps.LatLng(46.099424,-70.284691),
        new google.maps.LatLng(46.100376,-70.254822),
        new google.maps.LatLng(46.150346,-70.240059),
        new google.maps.LatLng(46.191002,-70.292931),
        new google.maps.LatLng(46.291443,-70.231133),
        new google.maps.LatLng(46.329624,-70.208817),
        new google.maps.LatLng(46.359487,-70.174141),
        new google.maps.LatLng(46.381044,-70.126762),
        new google.maps.LatLng(46.409694,-70.096207),
        new google.maps.LatLng(46.415612,-70.057411),
        new google.maps.LatLng(46.696316,-69.9963),	    //Lac-Frontiere
        new google.maps.LatLng(47.459898,-69.225197),
        new google.maps.LatLng(47.457112,-69.178505),
        new google.maps.LatLng(47.433661,-69.062462),
        new google.maps.LatLng(47.42739,-69.043236),
        new google.maps.LatLng(47.408109,-69.029846),
        new google.maps.LatLng(47.383474,-69.040146),
        new google.maps.LatLng(47.377895,-69.054222),
        new google.maps.LatLng(47.306939,-69.048386),
        new google.maps.LatLng(47.253602,-69.045982),
        new google.maps.LatLng(47.206508,-68.943329),
        new google.maps.LatLng(47.178746,-68.900414),
        new google.maps.LatLng(47.215604,-68.813553),
        new google.maps.LatLng(47.241949,-68.715019),
        new google.maps.LatLng(47.258495,-68.59314),     //Fort Kent
        new google.maps.LatLng(47.287846,-68.57769),
        new google.maps.LatLng(47.296229,-68.513489),
        new google.maps.LatLng(47.297161,-68.47229),
        new google.maps.LatLng(47.288079,-68.414955),
        new google.maps.LatLng(47.288079,-68.37719),
        new google.maps.LatLng(47.304145,-68.385086),
        new google.maps.LatLng(47.349291,-68.3741),
        new google.maps.LatLng(47.35999,-68.327408),
        new google.maps.LatLng(47.35999,-68.285866),
        new google.maps.LatLng(47.353711,-68.230591),
        new google.maps.LatLng(47.325792,-68.15712),
        new google.maps.LatLng(47.306939,-68.14991),
        new google.maps.LatLng(47.27457,-68.086395),
        new google.maps.LatLng(47.237753,-68.018417),
        new google.maps.LatLng(47.206975,-67.970352),
        new google.maps.LatLng(47.196945,-67.951813),
        new google.maps.LatLng(47.147933,-67.909584),    //saint-leonard-parent
        new google.maps.LatLng(47.100278,-67.867012),
        new google.maps.LatLng(47.06755,-67.790108),
        new google.maps.LatLng(45.941601,-67.780151),
        new google.maps.LatLng(45.918199,-67.750626),
        new google.maps.LatLng(45.897655,-67.767792),
        new google.maps.LatLng(45.882839,-67.802811),
        new google.maps.LatLng(45.823536,-67.755432),
        new google.maps.LatLng(45.730629,-67.782211),
        new google.maps.LatLng(45.676921,-67.769852),
        new google.maps.LatLng(45.688914,-67.732773),
        new google.maps.LatLng(45.681239,-67.7108),
        new google.maps.LatLng(45.623643,-67.641449),
        new google.maps.LatLng(45.605391,-67.452621),
        new google.maps.LatLng(45.573678,-67.422409),
        new google.maps.LatLng(45.502978,-67.416916),
        new google.maps.LatLng(45.488539,-67.501373),
        new google.maps.LatLng(45.377232,-67.418289),
        new google.maps.LatLng(45.27392,-67.477341),
        new google.maps.LatLng(45.125382,-67.340012),
        new google.maps.LatLng(45.190748,-67.285767),
        new google.maps.LatLng(45.163642,-67.164917),	//champlain
        new google.maps.LatLng(45.110362,-67.109985),
        new google.maps.LatLng(44.953623,-67.020721),
        new google.maps.LatLng(44.910359,-66.968536),
        new google.maps.LatLng(44.82763,-66.96373),
        new google.maps.LatLng(44.825195,-66.932831),
        new google.maps.LatLng(44.794018,-66.885452),
        new google.maps.LatLng(44.775499,-66.902618),
        new google.maps.LatLng(44.698433,-67.031708),	//near end of line (on map) that represents coastal border
        new google.maps.LatLng(44.134913,-68.230591),
        new google.maps.LatLng(43.992815,-68.626099),
        new google.maps.LatLng(43.940428,-69.185028),	//tenants harbor
        new google.maps.LatLng(43.693694,-69.833221),
        new google.maps.LatLng(43.560491,-70.197144),
        new google.maps.LatLng(43.358137,-70.426483),
        new google.maps.LatLng(43.219188,-70.573425),
        new google.maps.LatLng(43.016697,-70.675049),    //bottom left
        new google.maps.LatLng(43.055844,-70.698395),
        new google.maps.LatLng(43.129052,-70.827484),
        new google.maps.LatLng(43.228194,-70.811005),    //rollinsford
        new google.maps.LatLng(43.335167,-70.9552),
        new google.maps.LatLng(43.478833,-70.97168),
        new google.maps.LatLng(43.574422,-70.973053),	// Great East Lake
        new google.maps.LatLng(44.76258,-71.038113)      //umbagog lake
    ];

    //Maryland  ( TODO: better borders all around )
    else if((state == 'MD')||(state=='Maryland'))
    stateBoundaries = [  
        new google.maps.LatLng(38.015 ,-75.387),     //bottom left-ish
        new google.maps.LatLng(37.979 ,-75.632),
        new google.maps.LatLng(37.883 ,-76.234),
        new google.maps.LatLng(37.993 ,-76.576),
        new google.maps.LatLng(38.042 ,-76.512),
        new google.maps.LatLng(38.144 ,-76.616),
        new google.maps.LatLng(38.155 ,-76.705),
        new google.maps.LatLng(38.065 ,-76.691),
        new google.maps.LatLng(38.164 ,-76.774),
        new google.maps.LatLng(38.195 ,-76.989),
        new google.maps.LatLng(38.4 ,-77.05),
        new google.maps.LatLng(38.351 ,-77.363),
        new google.maps.LatLng(38.357 ,-77.29),
        new google.maps.LatLng(38.438 ,-77.37),
        new google.maps.LatLng(38.394 ,-77.312),
        new google.maps.LatLng(38.659 ,-77.243),
        new google.maps.LatLng(38.72 ,-77.045),
        new google.maps.LatLng(38.911 ,-77.101),
        new google.maps.LatLng(38.934 ,-77.12),
        new google.maps.LatLng(39.077 ,-77.463),
        new google.maps.LatLng(39.14 ,-77.526),
        new google.maps.LatLng(39.225 ,-77.458),
        new google.maps.LatLng(39.307 ,-77.636),
        new google.maps.LatLng(39.31 ,-77.648),
        new google.maps.LatLng(39.321 ,-77.719),     //top of virginia
        new google.maps.LatLng(39.496 ,-77.766),
        new google.maps.LatLng(39.552 ,-77.887),
        new google.maps.LatLng(39.603 ,-77.834),
        new google.maps.LatLng(39.696 ,-78.174),
        new google.maps.LatLng(39.624 ,-78.428),
        new google.maps.LatLng(39.516 ,-78.468),
        new google.maps.LatLng(39.544 ,-78.687),
        new google.maps.LatLng(39.645 ,-78.776),
        new google.maps.LatLng(39.441 ,-78.958),
        new google.maps.LatLng(39.474 ,-79.105),
        new google.maps.LatLng(39.202 ,-79.473),
        new google.maps.LatLng(39.721 ,-79.477),     //top left
        new google.maps.LatLng(39.721 ,-75.789),      //top right
        new google.maps.LatLng(38.46 ,-75.693),
        new google.maps.LatLng(38.451 ,-75.093),
        new google.maps.LatLng(38.451 ,-75.058),
        new google.maps.LatLng(38.451 ,-75.049)      //coast end
	];

    //Massachusetts ( TODO: better CT border and Coastal border )
    else if((state == 'MA')||(state=='Massachusetts'))
    stateBoundaries = [   
        new google.maps.LatLng(42.871 ,-70.817),         //top right
        new google.maps.LatLng(42.859 ,-71.031),
        new google.maps.LatLng(42.697 ,-71.295),
        new google.maps.LatLng(42.727 ,-72.459),
        new google.maps.LatLng(42.746 ,-73.265),         //top left (top of NY border)
        new google.maps.LatLng(42.086248,-73.508148),
        new google.maps.LatLng(42.049675,-73.496861),    //bottom left (bottom of NY border)
        new google.maps.LatLng(42.049 ,-73.488),         // left of CT border
        new google.maps.LatLng(42.023 ,-71.8),   
        new google.maps.LatLng(42.008 ,-71.8),           //Left of RI border
        new google.maps.LatLng(42.018 ,-71.381),
        new google.maps.LatLng(41.776 ,-71.318),
        new google.maps.LatLng(41.775 ,-71.316),
        new google.maps.LatLng(41.763 ,-71.287),
        new google.maps.LatLng(41.759 ,-71.279),
        new google.maps.LatLng(41.718 ,-71.232),
        new google.maps.LatLng(41.674 ,-71.195),
        new google.maps.LatLng(41.66 ,-71.136),
        new google.maps.LatLng(41.647 ,-71.134),
        new google.maps.LatLng(41.496 ,-71.121),          //bottom of RI border
		new google.maps.LatLng(41.000 ,-70.25),
		new google.maps.LatLng(41.000 ,-69.95),
		new google.maps.LatLng(41.500 ,-69.70),
		new google.maps.LatLng(41.800 ,-69.70)
    ];

    //Michigan (TODO: west coastal border could be better)
    else if((state == 'MI')||(state=='Michigan'))
    stateBoundaries = [  
        new google.maps.LatLng(42.493365,-87.020302),    //bottom of Wisconsin
        new google.maps.LatLng(42.73264,-87.053947),
        new google.maps.LatLng(43.378602,-87.147331),
        new google.maps.LatLng(43.499507,-87.137032),
        new google.maps.LatLng(43.670355,-87.125359),
        new google.maps.LatLng(43.733399,-87.114029),
        new google.maps.LatLng(43.999483,-87.046051),
        new google.maps.LatLng(44.130232,-87.013092),
        new google.maps.LatLng(44.161273,-87.000046),
        new google.maps.LatLng(44.499934,-86.85482),
        new google.maps.LatLng(44.881661,-86.685219),
        new google.maps.LatLng(45.081036,-86.499481),
        new google.maps.LatLng(45.236218,-86.250916),  //
        new google.maps.LatLng(45.437008,-86.750793), 
        new google.maps.LatLng(45.437008,-87.102356), 
        new google.maps.LatLng(45.205263,-87.40448), 
        new google.maps.LatLng(45.0774,-87.442932), 
        new google.maps.LatLng(45.096 ,-87.585),     //marionette
        new google.maps.LatLng(45.175 ,-87.735),   
        new google.maps.LatLng(45.369 ,-87.66),
        new google.maps.LatLng(45.353 ,-87.884),
        new google.maps.LatLng(45.685 ,-87.783),
        new google.maps.LatLng(45.81 ,-88.131),
        new google.maps.LatLng(45.921 ,-88.103),
        new google.maps.LatLng(46.272 ,-89.8),
        new google.maps.LatLng(46.3 ,-89.929),
        new google.maps.LatLng(46.566 ,-90.417),      //top of wi land border
        new google.maps.LatLng(48.014272,-89.483643), //top of minnesota
        new google.maps.LatLng(47.975214,-89.338074),
        new google.maps.LatLng(48.244797,-88.676147),
        new google.maps.LatLng(48.303294,-88.371277),
        new google.maps.LatLng(46.888355,-84.858398),
        new google.maps.LatLng(46.630579,-84.759521),
        new google.maps.LatLng(46.454889,-84.550781),
        new google.maps.LatLng(46.451105,-84.476624),
        new google.maps.LatLng(46.528635,-84.119568),
        new google.maps.LatLng(46.059891,-83.952026),
        new google.maps.LatLng(46.120845,-83.825684),
        new google.maps.LatLng(45.99887,-83.432922),
        new google.maps.LatLng(45.775186,-83.636169),
        new google.maps.LatLng(45.771355,-83.482361),
        new google.maps.LatLng(45.338633,-82.518311),
        new google.maps.LatLng(43.58238,-82.120056),
        new google.maps.LatLng(43.002639,-82.408447), //samia
        new google.maps.LatLng(42.813537,-82.482605),
        new google.maps.LatLng(42.763146,-82.466125),
        new google.maps.LatLng(42.616781,-82.515564),
        new google.maps.LatLng(42.54701,-82.610321),
        new google.maps.LatLng(42.554092,-82.637787),
        new google.maps.LatLng(42.37072,-82.831421),
        new google.maps.LatLng(42.31997,-83.049774),
        new google.maps.LatLng(42.126747,-83.118439),	//Grosse lle
        new google.maps.LatLng(42.039094,-83.148651),
        new google.maps.LatLng(41.957448,-83.114319),    //bottom right-ish
        new google.maps.LatLng(41.731355,-83.416443),
        new google.maps.LatLng(41.733 ,-83.454),         
        new google.maps.LatLng(41.696 ,-84.806),
        new google.maps.LatLng(41.76 ,-84.806),       //right of IN border
        new google.maps.LatLng(41.76 ,-86.825)
    ];

    //Minnesota
    else if((state == 'MN')||(state=='Minnesota'))
    stateBoundaries = [   
        new google.maps.LatLng(43.5 ,-96.453),       //bottom left
        new google.maps.LatLng(45.301 ,-96.453),
        new google.maps.LatLng(45.606 ,-96.857),
        new google.maps.LatLng(45.817 ,-96.585),
        new google.maps.LatLng(45.935 ,-96.562),      //top of SD border
        new google.maps.LatLng(46.316 ,-96.596),
        new google.maps.LatLng(46.709 ,-96.788),
        new google.maps.LatLng(46.72 ,-96.785),
        new google.maps.LatLng(47.598 ,-96.851),
        new google.maps.LatLng(48.143 ,-97.146),
        new google.maps.LatLng(48.563 ,-97.174),
        new google.maps.LatLng(48.686 ,-97.094),
        new google.maps.LatLng(49 ,-97.229),          //top left
        new google.maps.LatLng(49.000042,-95.155334),
        new google.maps.LatLng(49.377008,-95.152588),
        new google.maps.LatLng(49.321542,-94.817505),
        new google.maps.LatLng(48.871941,-94.680176),
        new google.maps.LatLng(48.830374,-94.699402),
        new google.maps.LatLng(48.776103,-94.691162),
        new google.maps.LatLng(48.694586,-94.441223),
        new google.maps.LatLng(48.631093,-93.856201),
        new google.maps.LatLng(48.523881,-93.815002),
        new google.maps.LatLng(48.589326,-93.46344),
        new google.maps.LatLng(48.640169,-93.210754),
        new google.maps.LatLng(48.631093,-92.949829),
        new google.maps.LatLng(48.545705,-92.636719),	//leatherdale landing
        new google.maps.LatLng(48.412796,-92.455444),
        new google.maps.LatLng(48.222843,-92.367554),
        new google.maps.LatLng(48.246626,-92.271423),
        new google.maps.LatLng(48.352599,-92.25769),
        new google.maps.LatLng(48.359899,-92.054443),
        new google.maps.LatLng(48.199049,-91.71936),
        new google.maps.LatLng(48.067068,-91.450195),
        new google.maps.LatLng(48.244797,-90.884399),	//saganaga lake
        new google.maps.LatLng(48.092757,-90.75531),
        new google.maps.LatLng(48.085419,-90.027466),
        new google.maps.LatLng(47.986245,-89.87915),
        new google.maps.LatLng(48.011975,-89.489136),	//top right
        new google.maps.LatLng(47.290408,-89.958801),
        new google.maps.LatLng(47.309034,-90.653687),
        new google.maps.LatLng(46.702202,-92.015991),
        new google.maps.LatLng(46.748801,-92.088432),     //superior 
        new google.maps.LatLng(46.664 ,-92.291),
        new google.maps.LatLng(46.074 ,-92.293),
        new google.maps.LatLng(45.891 ,-92.712),
        new google.maps.LatLng(45.707 ,-92.87),
        new google.maps.LatLng(45.574 ,-92.882),
        new google.maps.LatLng(45.438 ,-92.645),
        new google.maps.LatLng(45.289 ,-92.761),
        new google.maps.LatLng(44.751 ,-92.808),
        new google.maps.LatLng(44.569 ,-92.549),
        new google.maps.LatLng(44.363 ,-91.965),
        new google.maps.LatLng(44.2 ,-91.875),
        new google.maps.LatLng(43.996 ,-91.431),
        new google.maps.LatLng(43.776 ,-91.245),
        new google.maps.LatLng(43.501 ,-91.217)     //bottom right
    ];

    //Mississippi
    else if((state == 'MS')||(state=='Mississippi'))
    stateBoundaries = [   
        new google.maps.LatLng(30.216 ,-89.604),     //bottom left
        new google.maps.LatLng(30.664 ,-89.848),
        new google.maps.LatLng(31.004 ,-89.733),
        new google.maps.LatLng(30.999 ,-91.637),
        new google.maps.LatLng(31.054 ,-91.559),
        new google.maps.LatLng(31.257 ,-91.653),
        new google.maps.LatLng(31.278 ,-91.513),
        new google.maps.LatLng(31.407 ,-91.577),
        new google.maps.LatLng(31.373 ,-91.472),
        new google.maps.LatLng(31.524 ,-91.521),
        new google.maps.LatLng(31.573 ,-91.406),
        new google.maps.LatLng(31.63 ,-91.515),
        new google.maps.LatLng(31.619 ,-91.403),
        new google.maps.LatLng(31.746 ,-91.369),
        new google.maps.LatLng(31.754 ,-91.262),
        new google.maps.LatLng(31.76 ,-91.363),
        new google.maps.LatLng(31.847 ,-91.341),
        new google.maps.LatLng(31.813 ,-91.254),
        new google.maps.LatLng(32.011 ,-91.08),
        new google.maps.LatLng(32.074 ,-91.156),
        new google.maps.LatLng(32.145 ,-91.004),
        new google.maps.LatLng(32.134 ,-91.167),
        new google.maps.LatLng(32.197 ,-91.163),
        new google.maps.LatLng(32.193 ,-90.994),
        new google.maps.LatLng(32.367 ,-90.877),
        new google.maps.LatLng(32.354 ,-90.994),
        new google.maps.LatLng(32.436 ,-90.967),
        new google.maps.LatLng(32.481 ,-91.115),
        new google.maps.LatLng(32.547 ,-91.095),
        new google.maps.LatLng(32.494 ,-90.987),
        new google.maps.LatLng(32.543 ,-91.072),
        new google.maps.LatLng(32.64 ,-91.013),
        new google.maps.LatLng(32.618 ,-91.152),
        new google.maps.LatLng(32.719 ,-91.053),
        new google.maps.LatLng(32.778 ,-91.163),
        new google.maps.LatLng(32.905 ,-91.063),
        new google.maps.LatLng(32.989 ,-91.105),
        new google.maps.LatLng(32.916 ,-91.209),
        new google.maps.LatLng(33.004 ,-91.166),      //top of LA Border
        new google.maps.LatLng(33.059 ,-91.121),
        new google.maps.LatLng(33.125 ,-91.201),
        new google.maps.LatLng(33.136 ,-91.093),
        new google.maps.LatLng(33.275 ,-91.043),
        new google.maps.LatLng(33.301 ,-91.141),
        new google.maps.LatLng(33.449 ,-91.059),
        new google.maps.LatLng(33.378 ,-91.148),
        new google.maps.LatLng(33.407 ,-91.209),
        new google.maps.LatLng(33.473 ,-91.125),
        new google.maps.LatLng(33.437 ,-91.231),
        new google.maps.LatLng(33.561 ,-91.23),
        new google.maps.LatLng(33.607 ,-91.129),
        new google.maps.LatLng(33.69 ,-91.222),
        new google.maps.LatLng(33.681 ,-91.037),
        new google.maps.LatLng(33.726 ,-91.14),
        new google.maps.LatLng(33.783 ,-90.986),
        new google.maps.LatLng(33.967 ,-91.09),
        new google.maps.LatLng(34.026 ,-90.893),
        new google.maps.LatLng(34.164 ,-90.927),
        new google.maps.LatLng(34.159 ,-90.809),
        new google.maps.LatLng(34.238 ,-90.934),
        new google.maps.LatLng(34.207 ,-90.848),
        new google.maps.LatLng(34.364 ,-90.762),
        new google.maps.LatLng(34.314 ,-90.661),
        new google.maps.LatLng(34.418 ,-90.573),
        new google.maps.LatLng(34.689 ,-90.569),
        new google.maps.LatLng(34.624 ,-90.535),
        new google.maps.LatLng(34.684 ,-90.461),
        new google.maps.LatLng(34.711 ,-90.567),
        new google.maps.LatLng(34.785 ,-90.547),
        new google.maps.LatLng(34.737 ,-90.459),
        new google.maps.LatLng(34.892 ,-90.464),
        new google.maps.LatLng(34.848 ,-90.306),
        new google.maps.LatLng(34.924 ,-90.241),
        new google.maps.LatLng(34.995 ,-90.308),     //top left
        new google.maps.LatLng(34.995 ,-88.2),       //top right
        new google.maps.LatLng(34.892 ,-88.097),
        new google.maps.LatLng(32.747 ,-88.368 ),
        new google.maps.LatLng(32.732 ,-88.37 ),
        new google.maps.LatLng(31.894 ,-88.473 ),
        new google.maps.LatLng(30.348 ,-88.394)      //bottom right
    ];

    //Missouri
    else if((state == 'MO')||(state=='Missouri'))
    stateBoundaries = [ 
        new google.maps.LatLng(36.625 ,-89.366), 
        new google.maps.LatLng(36.567 ,-89.236),
        new google.maps.LatLng(36.666 ,-89.159), 
        new google.maps.LatLng(36.982 ,-89.133),     //bottom of IL border
        new google.maps.LatLng(37.072 ,-89.254), 
        new google.maps.LatLng(36.989 ,-89.276), 
        new google.maps.LatLng(37.038 ,-89.376),
        new google.maps.LatLng(37.261 ,-89.522), 
        new google.maps.LatLng(37.387 ,-89.421), 
        new google.maps.LatLng(37.693 ,-89.517),
        new google.maps.LatLng(37.903 ,-89.839), 
        new google.maps.LatLng(37.88 ,-89.949),
        new google.maps.LatLng(37.958 ,-89.925), 
        new google.maps.LatLng(38.212 ,-90.351),
        new google.maps.LatLng(38.326 ,-90.373),  
        new google.maps.LatLng(38.844 ,-90.109), 
        new google.maps.LatLng(38.967 ,-90.451),
        new google.maps.LatLng(38.888 ,-90.625),
        new google.maps.LatLng(39.257 ,-90.731), 
        new google.maps.LatLng(39.728 ,-91.367), 
        new google.maps.LatLng(40.125 ,-91.51),
        new google.maps.LatLng(40.378 ,-91.419),      //top right
        new google.maps.LatLng(40.4 ,-91.5),
        new google.maps.LatLng(40.403 ,-91.505),
        new google.maps.LatLng(40.614 ,-91.728),
        new google.maps.LatLng(40.585 ,-95.765),     //top left
        new google.maps.LatLng(40.538 ,-95.652),
        new google.maps.LatLng(40.311 ,-95.656),
        new google.maps.LatLng(40.242 ,-95.477),
        new google.maps.LatLng(40.041 ,-95.415),
        new google.maps.LatLng(40 ,-95.307),         //top of KS border
        new google.maps.LatLng(39.874 ,-95.13),
        new google.maps.LatLng(39.887 ,-94.928),
        new google.maps.LatLng(39.744 ,-94.861),
        new google.maps.LatLng(39.741 ,-94.962),
        new google.maps.LatLng(39.542 ,-95.109),
        new google.maps.LatLng(39.386 ,-94.882),
        new google.maps.LatLng(39.213 ,-94.827),
        new google.maps.LatLng(39.148 ,-94.588),
        new google.maps.LatLng(36.998 ,-94.618),     //top of OK border
        new google.maps.LatLng(36.499 ,-94.617),     //bottom left
        new google.maps.LatLng(36.499 ,-90.802),
        new google.maps.LatLng(36.499 ,-90.789),
        new google.maps.LatLng(36.497 ,-90.151),
        new google.maps.LatLng(36.303 ,-90.064),
        new google.maps.LatLng(35.996 ,-90.377),  
        new google.maps.LatLng(36.001 ,-89.733),     //bottom right
        new google.maps.LatLng(36.134 ,-89.592),
        new google.maps.LatLng(36.25 ,-89.698),
        new google.maps.LatLng(36.25 ,-89.537),
        new google.maps.LatLng(36.323 ,-89.62),
        new google.maps.LatLng(36.356 ,-89.514),
        new google.maps.LatLng(36.498 ,-89.539),
        new google.maps.LatLng(36.578868,-89.520378),
        new google.maps.LatLng(36.497 ,-89.485),
        new google.maps.LatLng(36.457464,-89.469223),
        new google.maps.LatLng(36.499 ,-89.417)      //top of TN border
    ];

 //Montana
    else if((state == 'MT')||(state=='Montana'))
    stateBoundaries = [   
        new google.maps.LatLng(49 ,-104.047),        //top right
        new google.maps.LatLng(45.944 ,-104.045),    //top of SD border
        new google.maps.LatLng(44.998 ,-104.057),    //bottom right
        new google.maps.LatLng(45.001 ,-111.054),
        new google.maps.LatLng(44.475 ,-111.052),    //left side of WY border
        new google.maps.LatLng(44.755 ,-111.383),
        new google.maps.LatLng(44.643 ,-111.514),
        new google.maps.LatLng(44.541 ,-111.471),
        new google.maps.LatLng(44.569 ,-112.284),
        new google.maps.LatLng(44.45 ,-112.383),
        new google.maps.LatLng(44.485 ,-112.778),
        new google.maps.LatLng(44.359 ,-112.854),
        new google.maps.LatLng(44.453 ,-113.004),
        new google.maps.LatLng(44.774 ,-113.135),
        new google.maps.LatLng(44.864 ,-113.451),
        new google.maps.LatLng(45.058 ,-113.45),
        new google.maps.LatLng(45.26 ,-113.689),
        new google.maps.LatLng(45.603 ,-113.807),
        new google.maps.LatLng(45.696 ,-113.937),
        new google.maps.LatLng(45.459 ,-114.332),
        new google.maps.LatLng(45.558 ,-114.563),
        new google.maps.LatLng(45.704 ,-114.495),
        new google.maps.LatLng(45.777 ,-114.562),
        new google.maps.LatLng(45.885 ,-114.386),
        new google.maps.LatLng(46.147 ,-114.525),
        new google.maps.LatLng(46.172 ,-114.444),
        new google.maps.LatLng(46.647 ,-114.319),
        new google.maps.LatLng(46.7 ,-114.769),
        new google.maps.LatLng(47.253 ,-115.317),
        new google.maps.LatLng(47.427 ,-115.756),
        new google.maps.LatLng(47.48 ,-115.627),
        new google.maps.LatLng(47.55 ,-115.754),
        new google.maps.LatLng(47.696 ,-115.723),
        new google.maps.LatLng(47.978 ,-116.047),
        new google.maps.LatLng(49 ,-116.048)         //top left
    ];

    //Nebraska
    else if((state == 'NE')||(state=='Nebraska'))
    stateBoundaries = [ 
        new google.maps.LatLng(43.002 ,-104.056),    //top left
        new google.maps.LatLng(42.998 ,-98.499),
        new google.maps.LatLng(42.762 ,-98.013),
        new google.maps.LatLng(42.858 ,-97.874),
        new google.maps.LatLng(42.851 ,-97.232),
        new google.maps.LatLng(42.489 ,-96.443),     //top right
        new google.maps.LatLng(42.167 ,-96.347),
        new google.maps.LatLng(41.798 ,-96.066),
        new google.maps.LatLng(41.608 ,-96.116),
        new google.maps.LatLng(41.601 ,-96.113),
        new google.maps.LatLng(41.452 ,-95.919),
        new google.maps.LatLng(41.346 ,-95.957),
        new google.maps.LatLng(41.302 ,-95.872),
        new google.maps.LatLng(41.201 ,-95.927),
        new google.maps.LatLng(41.177 ,-95.841),
        new google.maps.LatLng(40.895 ,-95.809),
        new google.maps.LatLng(40.734 ,-95.888),
        new google.maps.LatLng(40.585 ,-95.765),     //top of MO border
        new google.maps.LatLng(40.538 ,-95.652),
        new google.maps.LatLng(40.311 ,-95.656),
        new google.maps.LatLng(40.242 ,-95.477),
        new google.maps.LatLng(40.041 ,-95.415),
        new google.maps.LatLng(40 ,-95.307),         //bottom right
        new google.maps.LatLng(40.003 ,-102.051),    //left of KS border
        new google.maps.LatLng(41.002,-102.051),     
        new google.maps.LatLng(41.001 ,-104.053)     //bottom left
    ];

    //Nevada
    else if((state == 'NV')||(state=='Nevada'))
    stateBoundaries = [   
        new google.maps.LatLng(37 ,-114.049),            //bottom of UT
        new google.maps.LatLng(41.994 ,-114.04),         //top right
        new google.maps.LatLng(42.002 ,-117.024),        //right of oregan
        new google.maps.LatLng(41.995 ,-119.998),        //top left
        new google.maps.LatLng(39 ,-120),
        new google.maps.LatLng(36.972 ,-117.166),
        new google.maps.LatLng(35.001 ,-114.632),        //bottom tip
        new google.maps.LatLng(35.181 ,-114.568), 
        new google.maps.LatLng(35.79 ,-114.698), 
        new google.maps.LatLng(35.804 ,-114.71),
        new google.maps.LatLng(36.103 ,-114.735), 
        new google.maps.LatLng(36.142 ,-114.369), 
        new google.maps.LatLng(36.142 ,-114.369), 
        new google.maps.LatLng(36.016 ,-114.21), 
        new google.maps.LatLng(36.194 ,-114.045), 
        new google.maps.LatLng(37 ,-114.049)             //top of AZ section
    ];

    //New Hampshire
    else if((state == 'NH')||(state=='New Hampshire'))
    stateBoundaries = [   
        new google.maps.LatLng(45.013 ,-71.502),     //top left
        new google.maps.LatLng(44.752 ,-71.632),
        new google.maps.LatLng(44.588 ,-71.535),
        new google.maps.LatLng(44.506 ,-71.58),
        new google.maps.LatLng(44.319 ,-72.032),
        new google.maps.LatLng(44.081 ,-72.033),
        new google.maps.LatLng(43.574 ,-72.38),
        new google.maps.LatLng(43.005 ,-72.445),
        new google.maps.LatLng(42.872 ,-72.555),
        new google.maps.LatLng(42.727 ,-72.459),      //bottom left
        new google.maps.LatLng(42.697 ,-71.295),
        new google.maps.LatLng(42.859 ,-71.031),
        new google.maps.LatLng(42.871 ,-70.817),         //bottom right
        new google.maps.LatLng(43.016697,-70.675049),    //bottom left of Maine
        new google.maps.LatLng(43.055844,-70.698395),
        new google.maps.LatLng(43.129052,-70.827484),
        new google.maps.LatLng(43.228194,-70.811005),    //rollinsford
        new google.maps.LatLng(43.335167,-70.9552),
        new google.maps.LatLng(43.478833,-70.97168),
        new google.maps.LatLng(43.574422,-70.973053),	// Great East Lake
        new google.maps.LatLng(44.76258,-71.038113),      //umbagog lake
        new google.maps.LatLng(45.306286,-71.08429),     //top right
        new google.maps.LatLng(45.241053,-71.143341),
        new google.maps.LatLng(45.302905,-71.28479),
        new google.maps.LatLng(45.237185,-71.442719),
        new google.maps.LatLng(45.078369,-71.496277)
    ];

    //New Jersey (TODO: need coast border)
    else if((state == 'NJ')||(state=='New Jersey'))
    stateBoundaries = [  
        new google.maps.LatLng(38.78781,-75.012589), //bottom of DE border
        new google.maps.LatLng(39.057584,-75.168457),
        new google.maps.LatLng(39.076776,-75.18837),
        new google.maps.LatLng(39.250334,-75.319519),
        new google.maps.LatLng(39.384202,-75.478134),
        new google.maps.LatLng(39.455282,-75.559845),
        new google.maps.LatLng(39.496093,-75.559158),
        new google.maps.LatLng(39.497153,-75.528259),
        new google.maps.LatLng(39.541117,-75.532379),
        new google.maps.LatLng(39.563353,-75.513153),
        new google.maps.LatLng(39.606217,-75.557098),
        new google.maps.LatLng(39.630019,-75.559845),
        new google.maps.LatLng(39.697149,-75.506973),
        new google.maps.LatLng(39.763158,-75.461655),
        new google.maps.LatLng(39.777408,-75.448608),
        new google.maps.LatLng(39.796403,-75.404663),
        new google.maps.LatLng(39.802 ,-75.415),     //bottom of PA border
        new google.maps.LatLng(39.844 ,-75.343), 
        new google.maps.LatLng(39.846 ,-75.339), 
        new google.maps.LatLng(39.884 ,-75.147), 
        new google.maps.LatLng(39.95528,-75.132751),
        new google.maps.LatLng(40.114 ,-74.831), 
        new google.maps.LatLng(40.119 ,-74.829), 
        new google.maps.LatLng(40.163 ,-74.724), 
        new google.maps.LatLng(40.412 ,-75.044), 
        new google.maps.LatLng(40.415 ,-75.051), 
        new google.maps.LatLng(40.54 ,-75.068), 
        new google.maps.LatLng(40.613 ,-75.201), 
        new google.maps.LatLng(40.75 ,-75.197), 
        new google.maps.LatLng(40.869 ,-75.051), 
        new google.maps.LatLng(40.986 ,-75.132), 
        new google.maps.LatLng(41.357 ,-74.695),     //top point
        new google.maps.LatLng(41.003 ,-73.908),
        new google.maps.LatLng(40.917 ,-73.918),
        new google.maps.LatLng(40.882 ,-73.934),
        new google.maps.LatLng(40.644 ,-74.161),
        new google.maps.LatLng(40.499 ,-74.259),      //bottom of NY border
		new google.maps.LatLng(40.499 ,-73.959),	  //right of that
		new google.maps.LatLng(40.199 ,-73.959),
		new google.maps.LatLng(39.500 ,-74.100),
    ];

    //New Mexico
    else if((state == 'NM')||(state=='New Mexico'))
    stateBoundaries = [   
        new google.maps.LatLng(36.999 ,-109.044),    //top left
        new google.maps.LatLng(36.002 ,-109.045),
        new google.maps.LatLng(34.96 ,-109.045),
        new google.maps.LatLng(31.332 ,-109.049),    //bottom left
        new google.maps.LatLng(31.33335,-108.208551),
        new google.maps.LatLng(31.78356,-108.208337), 
        new google.maps.LatLng(31.784 ,-106.528),    //bottom of TX border
        new google.maps.LatLng(31.869 ,-106.634),
        new google.maps.LatLng(32 ,-106.618),
        new google.maps.LatLng(32 ,-103.064),
        new google.maps.LatLng(36.500348,-103.041919),
        new google.maps.LatLng(36.5 ,-103.001),      //top of TX border
        new google.maps.LatLng(37 ,-103.001)         //top of OK border
    ];

    //New York (TODO: border around Island)
    else if((state == 'NY')||(state=='New York'))
    stateBoundaries = [  
        new google.maps.LatLng(42.049 ,-73.488), // top of CT border
        new google.maps.LatLng(41.295 ,-73.551), 
        new google.maps.LatLng(41.212 ,-73.482), 
        new google.maps.LatLng(41.101 ,-73.728), 
        new google.maps.LatLng(40.991 ,-73.659),  // bottom of CT border
        
        //insert Island here
		new google.maps.LatLng(40.991 ,-72.9259),
		new google.maps.LatLng(41.315 ,-71.912),
		new google.maps.LatLng(41.015 ,-71.8),
		new google.maps.LatLng(40.850 ,-72.259),
		new google.maps.LatLng(40.591 ,-72.659),
		
        
        new google.maps.LatLng(40.499 ,-74.259),     //bottom of NJ border
        new google.maps.LatLng(40.644 ,-74.161),
        new google.maps.LatLng(40.882 ,-73.934),
        new google.maps.LatLng(40.917 ,-73.918),
        new google.maps.LatLng(41.003 ,-73.908),
        new google.maps.LatLng(41.357 ,-74.695),     //right of PA border
        new google.maps.LatLng(41.481 ,-74.984),
        new google.maps.LatLng(41.812 ,-75.071),
        new google.maps.LatLng(41.864 ,-75.261),
        new google.maps.LatLng(42.006 ,-75.363),
        new google.maps.LatLng(41.999 ,-79.761),     //bottom left
        new google.maps.LatLng(42.269 ,-79.762),      
        new google.maps.LatLng(42.515892,-79.762115), //top left
        new google.maps.LatLng(42.805477,-78.997192),
        new google.maps.LatLng(42.843751,-78.925781),
        new google.maps.LatLng(42.953407,-78.931274),
        new google.maps.LatLng(43.004647,-79.020538),
        new google.maps.LatLng(43.055844,-78.999939),
        new google.maps.LatLng(43.077916,-79.074097),
        new google.maps.LatLng(43.121034,-79.067917),
        new google.maps.LatLng(43.171633,-79.052124),
        new google.maps.LatLng(43.262206,-79.070663),	//Niagara-on-the-Lake
        new google.maps.LatLng(43.44943,-79.199066),
        new google.maps.LatLng(43.634584,-78.688202),
        new google.maps.LatLng(43.632596,-78.373718),
        new google.maps.LatLng(43.630111,-77.248993),
        new google.maps.LatLng(43.629614,-76.775894),
        new google.maps.LatLng(44.094983,-76.436691),
        new google.maps.LatLng(44.134174,-76.352921),	//Point Alexandria
        new google.maps.LatLng(44.198944,-76.312408),
        new google.maps.LatLng(44.20362,-76.285973),
        new google.maps.LatLng(44.203374,-76.244774),
        new google.maps.LatLng(44.214448,-76.205978),
        new google.maps.LatLng(44.239542,-76.164093),
        new google.maps.LatLng(44.280604,-76.161346),
        new google.maps.LatLng(44.296333,-76.130791),
        new google.maps.LatLng(44.299282,-76.097488),
        new google.maps.LatLng(44.331216,-76.045647),
        new google.maps.LatLng(44.347177,-76.000671),
        new google.maps.LatLng(44.345704,-75.973892),
        new google.maps.LatLng(44.343003,-75.970116),
        new google.maps.LatLng(44.368533,-75.921707),
        new google.maps.LatLng(44.367796,-75.912094),
        new google.maps.LatLng(44.432309,-75.82077),
        new google.maps.LatLng(44.471766,-75.807037),
        new google.maps.LatLng(44.516093,-75.765495),
        new google.maps.LatLng(44.543261,-75.72567),	    //hillcrest (canada side)
        new google.maps.LatLng(44.60929,-75.634689),
        new google.maps.LatLng(44.705022,-75.5056),
        new google.maps.LatLng(44.756486,-75.423889),
        new google.maps.LatLng(44.772087,-75.413589),
        new google.maps.LatLng(44.773305,-75.396423),
        new google.maps.LatLng(44.788414,-75.369301),
        new google.maps.LatLng(44.808878,-75.343552),
        new google.maps.LatLng(44.806442,-75.333252),
        new google.maps.LatLng(44.826413,-75.30201),
        new google.maps.LatLng(44.836882,-75.306816),	//Iroquois Golf Club
        new google.maps.LatLng(44.856842,-75.256004),
        new google.maps.LatLng(44.866576,-75.241585),
        new google.maps.LatLng(44.877282,-75.218925),
        new google.maps.LatLng(44.89285,-75.165367),
        new google.maps.LatLng(44.914736,-75.134125),
        new google.maps.LatLng(44.926891,-75.096016),
        new google.maps.LatLng(44.934426,-75.05928),
        new google.maps.LatLng(44.958725,-75.004692),
        new google.maps.LatLng(44.971356,-74.998512),
        new google.maps.LatLng(44.983256,-74.971733),
        new google.maps.LatLng(44.983256,-74.907532),
        new google.maps.LatLng(44.999767,-74.887619),
        new google.maps.LatLng(45.015787,-74.826508),
        new google.maps.LatLng(45.014817,-74.801445),
        new google.maps.LatLng(45.004622,-74.792519),
        new google.maps.LatLng(45.005593,-74.76368),
        new google.maps.LatLng(44.994911,-74.759216),
        new google.maps.LatLng(44.990784,-74.730721),
        new google.maps.LatLng(45.003166,-74.701195),
        new google.maps.LatLng(45.000738,-74.672356),
        new google.maps.LatLng(44.999767,-74.660683),    // Saint-Regis
        new google.maps.LatLng(44.996125,-74.437523),
        new google.maps.LatLng(44.991755,-74.336929),
        new google.maps.LatLng(44.991998,-74.222946),
        new google.maps.LatLng(44.99127,-74.149475),
        new google.maps.LatLng(45.000495,-73.884087),
        new google.maps.LatLng(45.002923,-73.696632),
        new google.maps.LatLng(45.007535,-73.479996),
        new google.maps.LatLng(45.01069,-73.343697),     //top right
        new google.maps.LatLng(43.752 ,-73.363),         
        new google.maps.LatLng(43.588 ,-73.431),
        new google.maps.LatLng(43.627 ,-73.304),
        new google.maps.LatLng(43.533 ,-73.242),
        new google.maps.LatLng(42.746 ,-73.265),         //top of MA border
        new google.maps.LatLng(42.086248,-73.508148),
        new google.maps.LatLng(42.049675,-73.496861)     //bottom of MA border
    ];

    //North Carolina (TODO: Need better coastal border)
    else if((state == 'NC')||(state=='North Carolina'))
    stateBoundaries = [ 
        new google.maps.LatLng(36.55 ,-75.929),      //top right
        new google.maps.LatLng(36.55 ,-75.966),
        new google.maps.LatLng(36.55 ,-75.884),
        new google.maps.LatLng(36.55 ,-75.894),
        new google.maps.LatLng(36.55 ,-75.988),
        new google.maps.LatLng(36.55 ,-76.006),
        new google.maps.LatLng(36.55 ,-76.035),
        new google.maps.LatLng(36.55 ,-76.156),
        new google.maps.LatLng(36.55 ,-76.161),
        new google.maps.LatLng(36.542 ,-78.917),
        new google.maps.LatLng(36.542 ,-79.138),
        new google.maps.LatLng(36.588 ,-81.677),     //top left
        new google.maps.LatLng(36.338 ,-81.706),
        new google.maps.LatLng(36.301 ,-81.908),
        new google.maps.LatLng(36.12 ,-82.033),
        new google.maps.LatLng(36.116 ,-82.354),
        new google.maps.LatLng(35.955 ,-82.561),
        new google.maps.LatLng(36.065 ,-82.638),
        new google.maps.LatLng(35.944 ,-82.898),
        new google.maps.LatLng(35.773 ,-82.993),
        new google.maps.LatLng(35.562 ,-83.499),
        new google.maps.LatLng(35.518 ,-83.881),
        new google.maps.LatLng(35.274 ,-84.046),
        new google.maps.LatLng(35.225 ,-84.29),
        new google.maps.LatLng(34.988 ,-84.322),      //left side of GA border
        new google.maps.LatLng(35 ,-83.108),
        new google.maps.LatLng(35.215 ,-82.394),
        new google.maps.LatLng(35.149 ,-81.032),
        new google.maps.LatLng(35.047 ,-81.043),
        new google.maps.LatLng(35.107 ,-80.935),
        new google.maps.LatLng(34.935 ,-80.782),
        new google.maps.LatLng(34.819 ,-80.797),
        new google.maps.LatLng(34.804 ,-79.675),
        new google.maps.LatLng(33.856 ,-78.556),      //bottom right
        new google.maps.LatLng(33.838483,-77.961731),
        new google.maps.LatLng(34.482788,-77.433701), 
        new google.maps.LatLng(34.580083,-76.533508),
        new google.maps.LatLng(35.218697,-75.52002),
        new google.maps.LatLng(35.588085,-75.451355),
        new google.maps.LatLng(35.721988,-75.484314)
    ];

    //North Dakota
    else if((state == 'ND')||(state=='North Dakota'))
    stateBoundaries = [  
        new google.maps.LatLng(49 ,-104.047),        //top left
        new google.maps.LatLng(45.944 ,-104.045),    //bottm left
        new google.maps.LatLng(45.935 ,-96.562),     //bottom right
        new google.maps.LatLng(46.316 ,-96.596),
        new google.maps.LatLng(46.709 ,-96.788),
        new google.maps.LatLng(46.72 ,-96.785),
        new google.maps.LatLng(47.598 ,-96.851),
        new google.maps.LatLng(48.143 ,-97.146),
        new google.maps.LatLng(48.563 ,-97.174),
        new google.maps.LatLng(48.686 ,-97.094),
        new google.maps.LatLng(49 ,-97.229)          //top right
    ];

    //Ohio
    else if((state == 'OH')||(state=='Ohio'))
    stateBoundaries = [   
        new google.maps.LatLng(39.105 ,-84.819),         //Bottom Left
        new google.maps.LatLng(39.119 ,-84.451),
        new google.maps.LatLng(38.808 ,-84.215),
        new google.maps.LatLng(38.761 ,-83.869),
        new google.maps.LatLng(38.626 ,-83.668),
        new google.maps.LatLng(38.703 ,-83.526),
        new google.maps.LatLng(38.596 ,-83.293),
        new google.maps.LatLng(38.755 ,-82.889),
        new google.maps.LatLng(38.59 ,-82.843),
        new google.maps.LatLng(38.421 ,-82.594),         //bottom of WV border
        new google.maps.LatLng(38.445 ,-82.328),
        new google.maps.LatLng(38.593 ,-82.274),
        new google.maps.LatLng(38.596 ,-82.268),
        new google.maps.LatLng(38.605 ,-82.176),
        new google.maps.LatLng(38.787 ,-82.221),
        new google.maps.LatLng(39.025 ,-82.037),
        new google.maps.LatLng(38.875 ,-81.899),
        new google.maps.LatLng(38.923 ,-81.766),
        new google.maps.LatLng(39.077 ,-81.814),
        new google.maps.LatLng(39.268 ,-81.686),
        new google.maps.LatLng(39.268 ,-81.57),
        new google.maps.LatLng(39.41 ,-81.453),
        new google.maps.LatLng(39.342 ,-81.375),
        new google.maps.LatLng(39.387 ,-81.217),
        new google.maps.LatLng(39.625 ,-80.878),
        new google.maps.LatLng(40.313 ,-80.602),
        new google.maps.LatLng(40.584 ,-80.667),
        new google.maps.LatLng(40.638 ,-80.519),    
        new google.maps.LatLng(41.977 ,-80.519),
        new google.maps.LatLng(42.327141,-80.519829),    //top right
        new google.maps.LatLng(41.999815,-81.708755),
        new google.maps.LatLng(41.677015,-82.398148),
        new google.maps.LatLng(41.675989,-82.681046),
        new google.maps.LatLng(41.863425,-83.069687),
        new google.maps.LatLng(41.957448,-83.114319),    //right of Michigan border
        new google.maps.LatLng(41.731355,-83.416443),
        new google.maps.LatLng(41.733 ,-83.454),     
        new google.maps.LatLng(41.696 ,-84.806)         //top left  
    ];

    //Oklahoma
    else if((state == 'OK')||(state=='Oklahoma'))
    stateBoundaries = [ 
        new google.maps.LatLng(36.999262 ,-103.001),     //top left
        new google.maps.LatLng(36.998166 ,-102.9879),
        new google.maps.LatLng(36.998714 ,-102.747574),
        new google.maps.LatLng(36.994875 ,-102.701569),
        new google.maps.LatLng(36.993 ,-102.041),        //left of KS border
        new google.maps.LatLng(36.998 ,-94.618),         //top right
        new google.maps.LatLng(36.499 ,-94.617),         //top of AK border
        new google.maps.LatLng(35.398 ,-94.431),
        new google.maps.LatLng(33.637 ,-94.485),         //bottom right
        new google.maps.LatLng(33.961 ,-95.227),
        new google.maps.LatLng(33.873 ,-95.28),
        new google.maps.LatLng(33.939 ,-95.6),
        new google.maps.LatLng(33.845 ,-95.776),
        new google.maps.LatLng(33.838 ,-96.147),
        new google.maps.LatLng(33.687 ,-96.352),
        new google.maps.LatLng(33.843 ,-96.625),
        new google.maps.LatLng(33.894 ,-96.587),
        new google.maps.LatLng(33.823 ,-96.758),
        new google.maps.LatLng(33.955 ,-96.985),
        new google.maps.LatLng(33.718 ,-97.135),
        new google.maps.LatLng(33.914 ,-97.218),
        new google.maps.LatLng(33.819 ,-97.427),
        new google.maps.LatLng(33.991 ,-97.669),
        new google.maps.LatLng(33.856 ,-97.84),
        new google.maps.LatLng(33.881 ,-97.97),
        new google.maps.LatLng(33.989 ,-97.946),
        new google.maps.LatLng(34.011 ,-98.091),
        new google.maps.LatLng(34.153 ,-98.117),
        new google.maps.LatLng(34.148 ,-98.35),
        new google.maps.LatLng(34.057 ,-98.458),
        new google.maps.LatLng(34.161 ,-98.631),
        new google.maps.LatLng(34.114 ,-98.741),
        new google.maps.LatLng(34.213 ,-99.186),
        new google.maps.LatLng(34.453 ,-99.361),
        new google.maps.LatLng(34.373 ,-99.405),
        new google.maps.LatLng(34.381 ,-99.71),
        new google.maps.LatLng(34.579164,-99.94606),
        new google.maps.LatLng(34.578104,-99.954386),
        new google.maps.LatLng(34.561354,-99.976873),
        new google.maps.LatLng(34.560506,-100.000305),
        new google.maps.LatLng(36.5 ,-100.003),
        new google.maps.LatLng(36.5 ,-103.001)           //bottom left
    ];

    //Oregon
    else if((state == 'OR')||(state=='Oregon'))
    stateBoundaries = [ 
        new google.maps.LatLng(41.998 ,-124.215),    //bottom left
        new google.maps.LatLng(42.002 ,-117.024),    //bottom right
        new google.maps.LatLng(43.83 ,-117.031),
        new google.maps.LatLng(44.17 ,-116.894),
        new google.maps.LatLng(44.273 ,-117.196),
        new google.maps.LatLng(44.393 ,-117.241),
        new google.maps.LatLng(44.727 ,-117.06),
        new google.maps.LatLng(44.881 ,-116.855),
        new google.maps.LatLng(45.025 ,-116.844),
        new google.maps.LatLng(45.603 ,-116.462),
        new google.maps.LatLng(45.752 ,-116.547),
        new google.maps.LatLng(45.824 ,-116.779),
        new google.maps.LatLng(45.995 ,-116.914),    //top right
        new google.maps.LatLng(46 ,-118.985),
        new google.maps.LatLng(45.699 ,-120.401),
        new google.maps.LatLng(45.746 ,-120.633),
        new google.maps.LatLng(45.606 ,-121.182),
        new google.maps.LatLng(45.705 ,-121.336),
        new google.maps.LatLng(45.707 ,-121.809),
        new google.maps.LatLng(45.544 ,-122.295),
        new google.maps.LatLng(45.657 ,-122.762),
        new google.maps.LatLng(46.084 ,-122.902),
        new google.maps.LatLng(46.161 ,-123.226),
        new google.maps.LatLng(46.224 ,-123.426),
        new google.maps.LatLng(46.262 ,-123.664),
        new google.maps.LatLng(46.256 ,-123.951),    
        new google.maps.LatLng(46.267 ,-123.97),
        new google.maps.LatLng(46.298 ,-124.028),
        new google.maps.LatLng(46.267 ,-124.034)     //top left
    ];

    //Pennsylvania
    else if((state == 'PA')||(state=='Pennsylvania'))
    stateBoundaries = [   
        new google.maps.LatLng(42.327141,-80.519829),    //top left
        new google.maps.LatLng(40.638 ,-80.519),         //top of WV border
        new google.maps.LatLng(39.721 ,-80.519),         //bottom left
        new google.maps.LatLng(39.721 ,-79.477),         //right of WV border
        new google.maps.LatLng(39.721 ,-75.789),         //right of Maryland Border
        new google.maps.LatLng(39.788 ,-75.721),
        new google.maps.LatLng(39.807 ,-75.693),
        new google.maps.LatLng(39.82304,-75.657692),
        new google.maps.LatLng(39.83675,-75.59967),
        new google.maps.LatLng(39.837805,-75.526886),
        new google.maps.LatLng(39.820667,-75.454102),
        new google.maps.LatLng(39.802 ,-75.415),         //bottom of NJ border  
        new google.maps.LatLng(39.844 ,-75.343), 
        new google.maps.LatLng(39.846 ,-75.339), 
        new google.maps.LatLng(39.884 ,-75.147), 
        new google.maps.LatLng(39.95528,-75.132751),
        new google.maps.LatLng(40.114 ,-74.831), 
        new google.maps.LatLng(40.119 ,-74.829), 
        new google.maps.LatLng(40.163 ,-74.724), 
        new google.maps.LatLng(40.412 ,-75.044), 
        new google.maps.LatLng(40.415 ,-75.051), 
        new google.maps.LatLng(40.54 ,-75.068), 
        new google.maps.LatLng(40.613 ,-75.201), 
        new google.maps.LatLng(40.75 ,-75.197), 
        new google.maps.LatLng(40.869 ,-75.051), 
        new google.maps.LatLng(40.986 ,-75.132), 
        new google.maps.LatLng(41.357 ,-74.695),         //top point of NJ     
        new google.maps.LatLng(41.481 ,-74.984),
        new google.maps.LatLng(41.812 ,-75.071),
        new google.maps.LatLng(41.864 ,-75.261),
        new google.maps.LatLng(42.006 ,-75.363),
        new google.maps.LatLng(41.999 ,-79.761),     
        new google.maps.LatLng(42.269 ,-79.762),      
        new google.maps.LatLng(42.515892,-79.762115)     //top right
		];

    //Rhode Island
    else if((state == 'RI')||(state=='Rhode Island'))
    stateBoundaries = [ 
        new google.maps.LatLng(41.423 ,-71.798), // bottom left
        new google.maps.LatLng(42.008 ,-71.8),   // top left
        new google.maps.LatLng(42.018 ,-71.381),
        new google.maps.LatLng(41.776 ,-71.318),
        new google.maps.LatLng(41.775 ,-71.316),
        new google.maps.LatLng(41.763 ,-71.287),
        new google.maps.LatLng(41.759 ,-71.279),
        new google.maps.LatLng(41.718 ,-71.232),
        new google.maps.LatLng(41.674 ,-71.195),
        new google.maps.LatLng(41.66 ,-71.136),
        new google.maps.LatLng(41.647 ,-71.134),
        new google.maps.LatLng(41.496 ,-71.121),  //bottom right
		new google.maps.LatLng(41.296 ,-71.121),
		new google.maps.LatLng(41.296 ,-71.8)
    ];

    //South Carolina
    else if((state == 'SC')||(state=='South Carolina'))
    stateBoundaries = [  
        new google.maps.LatLng(33.183537,-79.170227),
        new google.maps.LatLng(32.99945,-79.354248),
        new google.maps.LatLng(32.669437,-79.881592),
        new google.maps.LatLng(32.032964,-80.837059),    //coast ( bottom tip of SC )
        new google.maps.LatLng(32.099299,-81.081333),    //savannah area
        new google.maps.LatLng(32.226 ,-81.144),
        new google.maps.LatLng(32.464 ,-81.186),
        new google.maps.LatLng(32.628 ,-81.418),
        new google.maps.LatLng(33.01 ,-81.495),
        new google.maps.LatLng(33.147 ,-81.751),
        new google.maps.LatLng(33.347 ,-81.94),
        new google.maps.LatLng(33.465 ,-81.929),
        new google.maps.LatLng(33.945 ,-82.557),
        new google.maps.LatLng(34.474 ,-82.877),
        new google.maps.LatLng(34.682 ,-83.342),
        new google.maps.LatLng(35 ,-83.108),             //top left
        new google.maps.LatLng(35.215 ,-82.394),
        new google.maps.LatLng(35.149 ,-81.032),
        new google.maps.LatLng(35.047 ,-81.043),
        new google.maps.LatLng(35.107 ,-80.935),
        new google.maps.LatLng(34.935 ,-80.782),
        new google.maps.LatLng(34.819 ,-80.797),
        new google.maps.LatLng(34.804 ,-79.675),
        new google.maps.LatLng(33.856 ,-78.556)          //top right
    ];
        
    //South Dakota
    else if((state == 'SD')||(state=='South Dakota'))
    stateBoundaries = [ 
        new google.maps.LatLng(45.944 ,-104.045),    //top left
        new google.maps.LatLng(44.998 ,-104.057),    //bottom of MT border   
        new google.maps.LatLng(43.002 ,-104.056),     //bottom left
        new google.maps.LatLng(42.998 ,-98.499),
        new google.maps.LatLng(42.762 ,-98.013),
        new google.maps.LatLng(42.858 ,-97.874),
        new google.maps.LatLng(42.851 ,-97.232),
        new google.maps.LatLng(42.489 ,-96.443),     //bottom right
        new google.maps.LatLng(42.737 ,-96.637),
        new google.maps.LatLng(43.12 ,-96.436),
        new google.maps.LatLng(43.269 ,-96.585),
        new google.maps.LatLng(43.385 ,-96.52),
        new google.maps.LatLng(43.5 ,-96.598),
        new google.maps.LatLng(43.5 ,-96.453),       //top of IA border
        new google.maps.LatLng(45.301 ,-96.453),
        new google.maps.LatLng(45.606 ,-96.857),
        new google.maps.LatLng(45.817 ,-96.585),
        new google.maps.LatLng(45.935 ,-96.562)     //top right
    ];

    //Tennessee
    else if((state == 'TN')||(state=='Tennessee'))
    stateBoundaries = [ 
        new google.maps.LatLng(36.588 ,-81.677),     //top right
        new google.maps.LatLng(36.338 ,-81.706),
        new google.maps.LatLng(36.301 ,-81.908),
        new google.maps.LatLng(36.12 ,-82.033),
        new google.maps.LatLng(36.116 ,-82.354),
        new google.maps.LatLng(35.955 ,-82.561),
        new google.maps.LatLng(36.065 ,-82.638),
        new google.maps.LatLng(35.944 ,-82.898),
        new google.maps.LatLng(35.773 ,-82.993),
        new google.maps.LatLng(35.562 ,-83.499),
        new google.maps.LatLng(35.518 ,-83.881),
        new google.maps.LatLng(35.274 ,-84.046),
        new google.maps.LatLng(35.225 ,-84.29),
        new google.maps.LatLng(34.988 ,-84.322),     //bottom right
        new google.maps.LatLng(34.984 ,-85.605),     //right of AL border
        new google.maps.LatLng(34.995 ,-88.2),       //right of MS border
        new google.maps.LatLng(34.995 ,-90.308),     //bottom left
        new google.maps.LatLng(35.139 ,-90.065),
        new google.maps.LatLng(35.265 ,-90.16),
        new google.maps.LatLng(35.277 ,-90.168),
        new google.maps.LatLng(35.386 ,-90.08),
        new google.maps.LatLng(35.421 ,-90.169),
        new google.maps.LatLng(35.429 ,-90.002),
        new google.maps.LatLng(35.547 ,-90.04),
        new google.maps.LatLng(35.514 ,-89.916),
        new google.maps.LatLng(35.592 ,-89.955),
        new google.maps.LatLng(35.639 ,-89.853),
        new google.maps.LatLng(35.729 ,-89.957),
        new google.maps.LatLng(35.814 ,-89.71),
        new google.maps.LatLng(35.909 ,-89.742),
        new google.maps.LatLng(35.892 ,-89.642),
        new google.maps.LatLng(36.001 ,-89.733),     //bottom of Missouri border
        new google.maps.LatLng(36.134 ,-89.592),
        new google.maps.LatLng(36.25 ,-89.698),
        new google.maps.LatLng(36.25 ,-89.537),
        new google.maps.LatLng(36.323 ,-89.62),
        new google.maps.LatLng(36.356 ,-89.514),
        new google.maps.LatLng(36.498 ,-89.539),
        new google.maps.LatLng(36.497 ,-89.485),
        new google.maps.LatLng(36.457464,-89.469223),
        new google.maps.LatLng(36.499 ,-89.417),      //top left
        new google.maps.LatLng(36.499 ,-89.417), 
        new google.maps.LatLng(36.497 ,-88.053), 
        new google.maps.LatLng(36.678 ,-88.07), 
        new google.maps.LatLng(36.601 ,-83.675)     //left of VA Border
    ];

    //Texas
    else if((state == 'TX')||(state=='Texas'))
    stateBoundaries = [   
        new google.maps.LatLng(29.999 ,-93.769),         //bottom right
        new google.maps.LatLng(30.137 ,-93.691),
        new google.maps.LatLng(30.538 ,-93.739),
        new google.maps.LatLng(31.031 ,-93.508),
        new google.maps.LatLng(31.509 ,-93.712),
        new google.maps.LatLng(31.585 ,-93.834),
        new google.maps.LatLng(31.777 ,-93.825),
        new google.maps.LatLng(31.992 ,-94.041),
        new google.maps.LatLng(33.019 ,-94.042),         //bottom of AK border
        new google.maps.LatLng(33.551 ,-94.043),
        new google.maps.LatLng(33.545 ,-94.386),   
        new google.maps.LatLng(33.637 ,-94.485),         //right of OK border
        new google.maps.LatLng(33.961 ,-95.227),
        new google.maps.LatLng(33.873 ,-95.28),
        new google.maps.LatLng(33.939 ,-95.6),
        new google.maps.LatLng(33.845 ,-95.776),
        new google.maps.LatLng(33.838 ,-96.147),
        new google.maps.LatLng(33.687 ,-96.352),
        new google.maps.LatLng(33.843 ,-96.625),
        new google.maps.LatLng(33.894 ,-96.587),
        new google.maps.LatLng(33.823 ,-96.758),
        new google.maps.LatLng(33.955 ,-96.985),
        new google.maps.LatLng(33.718 ,-97.135),
        new google.maps.LatLng(33.914 ,-97.218),
        new google.maps.LatLng(33.819 ,-97.427),
        new google.maps.LatLng(33.991 ,-97.669),
        new google.maps.LatLng(33.856 ,-97.84),
        new google.maps.LatLng(33.881 ,-97.97),
        new google.maps.LatLng(33.989 ,-97.946),
        new google.maps.LatLng(34.011 ,-98.091),
        new google.maps.LatLng(34.153 ,-98.117),
        new google.maps.LatLng(34.148 ,-98.35),
        new google.maps.LatLng(34.057 ,-98.458),
        new google.maps.LatLng(34.161 ,-98.631),
        new google.maps.LatLng(34.114 ,-98.741),
        new google.maps.LatLng(34.213 ,-99.186),
        new google.maps.LatLng(34.453 ,-99.361),
        new google.maps.LatLng(34.373 ,-99.405),
        new google.maps.LatLng(34.381 ,-99.71),
        new google.maps.LatLng(34.579164,-99.94606),
        new google.maps.LatLng(34.578104,-99.954386),
        new google.maps.LatLng(34.561354,-99.976873),
        new google.maps.LatLng(34.560506,-100.000305),
        new google.maps.LatLng(36.5 ,-100.003),
        new google.maps.LatLng(36.5 ,-103.001),          //top left
        new google.maps.LatLng(36.500348,-103.041919),
        new google.maps.LatLng(32 ,-103.064),
        new google.maps.LatLng(32 ,-106.618),
        new google.maps.LatLng(31.869 ,-106.634),
        new google.maps.LatLng(31.784 ,-106.528),        //bottom of NM Border
        
        //Mexican Border
        new google.maps.LatLng(31.461468,-106.199341),
        new google.maps.LatLng(30.272707,-104.761505),
        new google.maps.LatLng(29.910901,-104.676361),
        new google.maps.LatLng(29.632879,-104.508605),
        new google.maps.LatLng(29.392346,-104.16481),    //el Mulato
        new google.maps.LatLng(28.972104,-103.154755),
        new google.maps.LatLng(28.985394,-103.115015),
        new google.maps.LatLng(29.224096,-102.866364),
        new google.maps.LatLng(29.760801,-102.386742),
        new google.maps.LatLng(29.782556,-101.929436),
        new google.maps.LatLng(29.332951,-100.940666),   //acuna (ciudad acuna)
        new google.maps.LatLng(26.397559,-99.08432),
        new google.maps.LatLng(25.836977,-97.394314),
        new google.maps.LatLng(25.956347,-97.146091),     //south tip
        
        //Coastal Border
        new google.maps.LatLng(26.145268,-97.166862),
        new google.maps.LatLng(26.225679,-97.177162),
        new google.maps.LatLng(26.290645,-97.190895),
        new google.maps.LatLng(26.410936,-97.219391),
        new google.maps.LatLng(26.809364,-97.349167),
        new google.maps.LatLng(27.045367,-97.378693),
        new google.maps.LatLng(27.205938,-97.365303),    //Yarborough
        new google.maps.LatLng(27.83456,-97.044575),
        new google.maps.LatLng(28.334604,-96.415329),
        new google.maps.LatLng(28.93305,-95.292664),     //freeport
        new google.maps.LatLng(29.683281,-93.834229),
        new google.maps.LatLng(29.764377,-93.891907),
        new google.maps.LatLng(29.795368,-93.927612)
    ];

    //Utah
    else if((state == 'UT')||(state=='Utah'))
    stateBoundaries = [   
        new google.maps.LatLng(41.994 ,-114.04),     //top left
        new google.maps.LatLng(37 ,-114.049),        //bottom left
        new google.maps.LatLng(36.999,-109.044),     //bottom right
        new google.maps.LatLng(41 ,-109.049),        //top right
        new google.maps.LatLng(41 ,-111.045),
        new google.maps.LatLng(42.001 ,-111.045)     //top of Wyoming
    ];

    //Vermont
    else if((state == 'VT')||(state=='Vermont'))
    stateBoundaries = [ 
        new google.maps.LatLng(42.727 ,-72.459),         //bottom right
        new google.maps.LatLng(42.872 ,-72.555),
        new google.maps.LatLng(43.005 ,-72.445),
        new google.maps.LatLng(43.574 ,-72.38),
        new google.maps.LatLng(44.081 ,-72.033),
        new google.maps.LatLng(44.319 ,-72.032),
        new google.maps.LatLng(44.506 ,-71.58),
        new google.maps.LatLng(44.588 ,-71.535),
        new google.maps.LatLng(44.752 ,-71.632),
        new google.maps.LatLng(45.013 ,-71.502),         //top right
        new google.maps.LatLng(45.01069,-73.343697),     //top left
        new google.maps.LatLng(43.752 ,-73.363),         
        new google.maps.LatLng(43.588 ,-73.431),
        new google.maps.LatLng(43.627 ,-73.304),
        new google.maps.LatLng(43.533 ,-73.242),
        new google.maps.LatLng(42.746 ,-73.265)          //bottom left
    ];
	
    //Washington (TODO: better coastline)
    //Virginia
    else if((state == 'VA')||(state=='Virginia'))
    stateBoundaries = [
        new google.maps.LatLng(38.015 ,-75.387),     //top right
        new google.maps.LatLng(37.979 ,-75.632),
        new google.maps.LatLng(37.883 ,-76.234),
        new google.maps.LatLng(37.993 ,-76.576),
        new google.maps.LatLng(38.042 ,-76.512),
        new google.maps.LatLng(38.144 ,-76.616),
        new google.maps.LatLng(38.155 ,-76.705),
        new google.maps.LatLng(38.065 ,-76.691),
        new google.maps.LatLng(38.164 ,-76.774),
        new google.maps.LatLng(38.195 ,-76.989),
        new google.maps.LatLng(38.4 ,-77.05),
        new google.maps.LatLng(38.351 ,-77.363),
        new google.maps.LatLng(38.357 ,-77.29),
        new google.maps.LatLng(38.438 ,-77.37),
        new google.maps.LatLng(38.394 ,-77.312),
        new google.maps.LatLng(38.659 ,-77.243),
        new google.maps.LatLng(38.72 ,-77.045),
        new google.maps.LatLng(38.911 ,-77.101),
        new google.maps.LatLng(38.934 ,-77.12),
        new google.maps.LatLng(39.077 ,-77.463),
        new google.maps.LatLng(39.14 ,-77.526),
        new google.maps.LatLng(39.225 ,-77.458),
        new google.maps.LatLng(39.307 ,-77.636),
        new google.maps.LatLng(39.31 ,-77.648),
        new google.maps.LatLng(39.321 ,-77.719),     //right of WV border
        new google.maps.LatLng(39.132 ,-77.828),
        new google.maps.LatLng(39.465 ,-78.347),
        new google.maps.LatLng(39.166 ,-78.405),
        new google.maps.LatLng(38.762 ,-78.869),
        new google.maps.LatLng(38.846 ,-78.999),
        new google.maps.LatLng(38.421 ,-79.28),
        new google.maps.LatLng(38.591 ,-79.649),
        new google.maps.LatLng(38.271 ,-79.788),
        new google.maps.LatLng(37.689 ,-80.307),
        new google.maps.LatLng(37.627 ,-80.221),
        new google.maps.LatLng(37.508 ,-80.299),
        new google.maps.LatLng(37.426 ,-80.466),
        new google.maps.LatLng(37.429 ,-80.859),
        new google.maps.LatLng(37.317 ,-80.896),
        new google.maps.LatLng(37.234 ,-81.225),
        new google.maps.LatLng(37.337 ,-81.362),
        new google.maps.LatLng(37.201 ,-81.677),
        new google.maps.LatLng(37.359 ,-81.927),
        new google.maps.LatLng(37.538 ,-81.968),      //left of WV border
        new google.maps.LatLng(37.121 ,-82.721),
        new google.maps.LatLng(36.897 ,-82.873),
        new google.maps.LatLng(36.852 ,-83.074),
        new google.maps.LatLng(36.745 ,-83.133),
        new google.maps.LatLng(36.601 ,-83.675),     //bottom left
        new google.maps.LatLng(36.588 ,-81.677),     //right of TN border
        new google.maps.LatLng(36.542 ,-79.138),
        new google.maps.LatLng(36.542 ,-78.917),
        new google.maps.LatLng(36.55 ,-76.161),
        new google.maps.LatLng(36.55 ,-76.156),
        new google.maps.LatLng(36.55 ,-76.035),
        new google.maps.LatLng(36.55 ,-76.006),
        new google.maps.LatLng(36.55 ,-75.988),
        new google.maps.LatLng(36.55 ,-75.894),
        new google.maps.LatLng(36.55 ,-75.884),
        new google.maps.LatLng(36.55 ,-75.966),
        new google.maps.LatLng(36.55 ,-75.929)      //bottom right 
    ];

    else if((state == 'WA')||(state=='Washington'))
    stateBoundaries = [ 
        new google.maps.LatLng(48.499318,-124.744263),   //top left
        new google.maps.LatLng(48.224673,-123.53714),
        new google.maps.LatLng(48.283193,-123.244629),   
        new google.maps.LatLng(48.422821,-123.11142),
        new google.maps.LatLng(48.691867,-123.270721),
        new google.maps.LatLng(48.766147,-123.005676),
        new google.maps.LatLng(48.831278,-123.00705),
        new google.maps.LatLng(49.001619,-123.32119),    
        new google.maps.LatLng(49.00252,-122.497902),
        new google.maps.LatLng(48.997339,-121.755638),
        new google.maps.LatLng(49.000042,-121.385193),
        new google.maps.LatLng(49.000042,-120.669022),
        new google.maps.LatLng(48.999366,-120.051727),
        new google.maps.LatLng(49.000042,-119.621887),
        new google.maps.LatLng(49.000042,-119.102097),
        new google.maps.LatLng(49.000042,-118.652687),
        new google.maps.LatLng(49.000267,-118.22216),
        new google.maps.LatLng(48.999817,-117.933769),
        new google.maps.LatLng(49.000493,-117.635765),
        new google.maps.LatLng(48.9991,-117.0323),       //top right
        new google.maps.LatLng(48.999 ,-117.031),
        new google.maps.LatLng(46.354 ,-117.06),
        new google.maps.LatLng(46.168 ,-116.921),
        new google.maps.LatLng(46.088 ,-116.981),
        new google.maps.LatLng(45.995 ,-116.914),        //bottom right
        new google.maps.LatLng(46 ,-118.985),
        new google.maps.LatLng(45.699 ,-120.401),
        new google.maps.LatLng(45.746 ,-120.633),
        new google.maps.LatLng(45.606 ,-121.182),
        new google.maps.LatLng(45.705 ,-121.336),
        new google.maps.LatLng(45.707 ,-121.809),
        new google.maps.LatLng(45.544 ,-122.295),
        new google.maps.LatLng(45.657 ,-122.762),
        new google.maps.LatLng(46.084 ,-122.902),
        new google.maps.LatLng(46.161 ,-123.226),
        new google.maps.LatLng(46.224 ,-123.426),
        new google.maps.LatLng(46.262 ,-123.664),
        new google.maps.LatLng(46.256 ,-123.951),    
        new google.maps.LatLng(46.267 ,-123.97),
        new google.maps.LatLng(46.298 ,-124.028),
        new google.maps.LatLng(46.267 ,-124.034)     //bottom left
    ];

    //West Virginia
    else if((state == 'WV')||(state=='West Virginia'))
    stateBoundaries = [  
        new google.maps.LatLng(39.321 ,-77.719),     //right of virginia border
        new google.maps.LatLng(39.132 ,-77.828),
        new google.maps.LatLng(39.465 ,-78.347),
        new google.maps.LatLng(39.166 ,-78.405),
        new google.maps.LatLng(38.762 ,-78.869),
        new google.maps.LatLng(38.846 ,-78.999),
        new google.maps.LatLng(38.421 ,-79.28),
        new google.maps.LatLng(38.591 ,-79.649),
        new google.maps.LatLng(38.271 ,-79.788),
        new google.maps.LatLng(37.689 ,-80.307),
        new google.maps.LatLng(37.627 ,-80.221),
        new google.maps.LatLng(37.508 ,-80.299),
        new google.maps.LatLng(37.426 ,-80.466),
        new google.maps.LatLng(37.429 ,-80.859),
        new google.maps.LatLng(37.317 ,-80.896),
        new google.maps.LatLng(37.234 ,-81.225),
        new google.maps.LatLng(37.337 ,-81.362),
        new google.maps.LatLng(37.201 ,-81.677),
        new google.maps.LatLng(37.359 ,-81.927),
        new google.maps.LatLng(37.538 ,-81.968),      //left of virigina border
        new google.maps.LatLng(37.678 ,-82.304),
        new google.maps.LatLng(37.93 ,-82.5),
        new google.maps.LatLng(37.98 ,-82.464),
        new google.maps.LatLng(38.162 ,-82.643),
        new google.maps.LatLng(38.421 ,-82.594),     //top of Kentucky Border
        new google.maps.LatLng(38.445 ,-82.328),
        new google.maps.LatLng(38.593 ,-82.274),
        new google.maps.LatLng(38.596 ,-82.268),
        new google.maps.LatLng(38.605 ,-82.176),
        new google.maps.LatLng(38.787 ,-82.221),
        new google.maps.LatLng(39.025 ,-82.037),
        new google.maps.LatLng(38.875 ,-81.899),
        new google.maps.LatLng(38.923 ,-81.766),
        new google.maps.LatLng(39.077 ,-81.814),
        new google.maps.LatLng(39.268 ,-81.686),
        new google.maps.LatLng(39.268 ,-81.57),
        new google.maps.LatLng(39.41 ,-81.453),
        new google.maps.LatLng(39.342 ,-81.375),
        new google.maps.LatLng(39.387 ,-81.217),
        new google.maps.LatLng(39.625 ,-80.878),
        new google.maps.LatLng(40.313 ,-80.602),
        new google.maps.LatLng(40.584 ,-80.667),
        new google.maps.LatLng(40.638 ,-80.519),   //top point
        new google.maps.LatLng(39.721 ,-80.519),   
        new google.maps.LatLng(39.721 ,-79.477),    //right of Pennsylvania border
        new google.maps.LatLng(39.202 ,-79.473),
        new google.maps.LatLng(39.474 ,-79.105),
        new google.maps.LatLng(39.441 ,-78.958),
        new google.maps.LatLng(39.645 ,-78.776),
        new google.maps.LatLng(39.544 ,-78.687),
        new google.maps.LatLng(39.516 ,-78.468),
        new google.maps.LatLng(39.624 ,-78.428),
        new google.maps.LatLng(39.696 ,-78.174),
        new google.maps.LatLng(39.603 ,-77.834),
        new google.maps.LatLng(39.552 ,-77.887),
        new google.maps.LatLng(39.496 ,-77.766),
        new google.maps.LatLng(39.321 ,-77.719)     //top of virginia
    ];

    //Wisconsin
    else if((state == 'WI')||(state=='Wisconsin'))
    stateBoundaries = [ 
        new google.maps.LatLng(43.501 ,-91.217),     //top of Iowa border
        new google.maps.LatLng(43.353 ,-91.206),
        new google.maps.LatLng(43.258 ,-91.062),
        new google.maps.LatLng(43.252 ,-91.059),
        new google.maps.LatLng(43.125 ,-91.178),
        new google.maps.LatLng(42.751 ,-91.065),
        new google.maps.LatLng(42.634 ,-90.707),
        new google.maps.LatLng(42.508 ,-90.64),      //bottom left
        new google.maps.LatLng(42.493365,-87.020302),    //bottom right
        new google.maps.LatLng(42.73264,-87.053947),
        new google.maps.LatLng(43.378602,-87.147331),
        new google.maps.LatLng(43.499507,-87.137032),
        new google.maps.LatLng(43.670355,-87.125359),
        new google.maps.LatLng(43.733399,-87.114029),
        new google.maps.LatLng(43.999483,-87.046051),
        new google.maps.LatLng(44.130232,-87.013092),
        new google.maps.LatLng(44.161273,-87.000046),
        new google.maps.LatLng(44.499934,-86.85482),
        new google.maps.LatLng(44.881661,-86.685219),
        new google.maps.LatLng(45.081036,-86.499481),
        new google.maps.LatLng(45.236218,-86.250916),  //
        new google.maps.LatLng(45.437008,-86.750793), 
        new google.maps.LatLng(45.437008,-87.102356), 
        new google.maps.LatLng(45.205263,-87.40448), 
        new google.maps.LatLng(45.0774,-87.442932), 
        new google.maps.LatLng(45.096 ,-87.585),     //marionette
        new google.maps.LatLng(45.175 ,-87.735),   
        new google.maps.LatLng(45.369 ,-87.66),
        new google.maps.LatLng(45.353 ,-87.884),
        new google.maps.LatLng(45.685 ,-87.783),
        new google.maps.LatLng(45.81 ,-88.131),
        new google.maps.LatLng(45.921 ,-88.103),
        new google.maps.LatLng(46.272 ,-89.8),
        new google.maps.LatLng(46.3 ,-89.929),
        new google.maps.LatLng(46.566 ,-90.417),      //top of wi right land border
        new google.maps.LatLng(47.290408,-89.958801),
        new google.maps.LatLng(47.309034,-90.653687),
        new google.maps.LatLng(46.702202,-92.015991),
        new google.maps.LatLng(46.748801,-92.088432),     //superior 
        new google.maps.LatLng(46.664 ,-92.291),
        new google.maps.LatLng(46.074 ,-92.293),
        new google.maps.LatLng(45.891 ,-92.712),
        new google.maps.LatLng(45.707 ,-92.87),
        new google.maps.LatLng(45.574 ,-92.882),
        new google.maps.LatLng(45.438 ,-92.645),
        new google.maps.LatLng(45.289 ,-92.761),
        new google.maps.LatLng(44.751 ,-92.808),
        new google.maps.LatLng(44.569 ,-92.549),
        new google.maps.LatLng(44.363 ,-91.965),
        new google.maps.LatLng(44.2 ,-91.875),
        new google.maps.LatLng(43.996 ,-91.431),
        new google.maps.LatLng(43.776 ,-91.245),
        new google.maps.LatLng(43.501 ,-91.217)     //bottom right
    ];
	
    //Wyoming
	else if((state == 'WY')||(state=='Wyoming'))
    stateBoundaries = [  
        new google.maps.LatLng(41.001 ,-104.053),    //bottom right
        new google.maps.LatLng(43.002 ,-104.056),    //top of NE border
        new google.maps.LatLng(44.998 ,-104.057),    //top right
        new google.maps.LatLng(45.001 ,-111.054),    //top left
        new google.maps.LatLng(44.475 ,-111.052),    //left side of WY border
        new google.maps.LatLng(42.001 ,-111.045),    //bottom of Idaho border
        new google.maps.LatLng(41 ,-111.045)         //bottom left
    ];
	
	 //West TX
	else if(state == 'WestTX')
    stateBoundaries = [  
		new google.maps.LatLng(31.98944183792288, -103.0517578125),
		new google.maps.LatLng(31.93817848559113, -107.0013427734375), //el paso
		new google.maps.LatLng(30.637912028341127, -105.29296875),
		new google.maps.LatLng(29.649868677972304, -104.61181640625),
		new google.maps.LatLng(28.92163128242129, -103.18359375),
		new google.maps.LatLng(29.745301662213596, -102.392578125), //big bend
		new google.maps.LatLng(29.496987596535767, -101.18408203125),
		new google.maps.LatLng(28.07198030177986, -100.08544921875), //south border of west TX
		new google.maps.LatLng(32.3882, -98.9792), //early texas
		new google.maps.LatLng(34.07086232376631, -98.54736328125), //highway to norman
		new google.maps.LatLng(34.60608459216928, -99.9755859375), 
		new google.maps.LatLng(36.474306755095235, -99.9755859375),
		new google.maps.LatLng(36.527294814546245, -102.9638671875)
    ];
	
	//North TX
	else if(state == 'NorthTX')
    stateBoundaries = [  
		new google.maps.LatLng(31.681433116625957, -96.48193359375), //mexia
		new google.maps.LatLng(32.3882, -98.9792), //early texas
		new google.maps.LatLng(34.07086232376631, -98.54736328125), //highway to norman
		new google.maps.LatLng(33.95247360616281, -95.218505859375), //red river highspot
		new google.maps.LatLng(33.56199537293026, -94.03610229492188), //ogden
		new google.maps.LatLng(31.877557643340015,-93.9111328125),
		new google.maps.LatLng(31.071755902820133,-93.53759765625),
		new google.maps.LatLng(30.543338954230222,-93.6474609375)
    ];
	
	//South TX
	else if(state == 'SouthTX')
    stateBoundaries = [  
		new google.maps.LatLng(28.07198030177986, -100.08544921875), //south border of west TX
		new google.maps.LatLng(32.3882, -98.9792), //early texas
		new google.maps.LatLng(31.681433116625957, -96.48193359375), //mexia
		new google.maps.LatLng(30.552800413453546, -96.943359375), //near tanglewood
		new google.maps.LatLng(27.761329874505233, -97.11090087890625), //port aransas
		new google.maps.LatLng(25.72073513441211, -97.31689453125),
		new google.maps.LatLng(26.450902223672646, -99.228515625)
    ];
	
	//East TX
	else if(state == 'EastTX')
    stateBoundaries = [  
		new google.maps.LatLng(30.552800413453546, -96.943359375), //near tanglewood
		new google.maps.LatLng(31.681433116625957, -96.48193359375), //mexia
		new google.maps.LatLng(30.543338954230222,-93.6474609375), //south of north region
		new google.maps.LatLng(29.661802757613785, -93.84796142578125),
		new google.maps.LatLng(27.761329874505233, -97.11090087890625) //port aransas
    ];
	
	else
		return null;
	
	return stateBoundaries;
}