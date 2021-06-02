<?php

require_once "_start.php";

/* If you get the error Cannot connect to database, you probably have to execute these two MySQL commands:
        CREATE DATABASE ot;
        GRANT ALL ON ot.* to 'ot'@'localhost' IDENTIFIED BY 'ot'" );

   Check that the tables exist and recreate them if necessary
*/
createTables($oApp->kfdb);


if (!file_exists(CATSDIR_RESOURCES)) {
    @mkdir(CATSDIR_RESOURCES, 0777, true);
    echo "Resources Directiory Created<br />";
}

if( !$oApp->sess->IsLogin() ) {
    echo "<head><link rel='icon' href='https://catherapyservices.ca/wp-content/uploads/2018/08/cropped-cats_icon-32x32.jpg' sizes='32x32'><link href='https://fonts.googleapis.com/css?family=Lato:300,400' rel='stylesheet'></head><div style='position:absolute; top:5px; left:5px;'><svg id='logoSVG' xmlns='http://www.w3.org/2000/svg' version='1.1' x='0px' y='0px' width='350px' viewBox='0 0 594.928 140.61'>
<path fill='#FFFFFF' d='M594.928,140.609C396.643,140.61,198.357,140.61,0,140.61
	C0,93.771,0,46.93,0,0c198.261,0,396.595,0,594.928,0C594.928,46.83,594.928,93.719,594.928,140.609z'/>
<path class='darkBlue' fill='#084378' d='M37.598,77.409c-9.579-25.744,14.497-61.155,39.465-58.335
	c0.176,1.398-1.047,1.984-1.726,2.835C67.144,32.194,60.032,43.053,57.39,56.218c-2.283,11.374,0.124,21.543,8.357,29.977
	c5.453,5.583,11.335,10.761,15.87,17.19c1.875,2.658,3.779,5.297,5.268,8.683c-10.495,0.777-19.89-1.611-28.695-7.062
	c3.617-7.736,3.567-14.969-1.848-21.51C51.51,77.66,45.13,75.946,37.598,77.409z'/>
<path class='lightBlue' fill='#65CDF5' d='M68.979,43.058c2.245-6.428,11.111-19.893,16.246-24.735
	c6.563-0.265,17.006,3.229,23.046,7.844c-3.26,6.852-3.463,13.796,1.146,20.293c4.677,6.594,11.361,8.463,18.931,7.265
	c6.916,18.374-5.276,43.134-20.281,50.557c-1.139-3.45-0.935-6.93-0.391-10.306c0.999-6.187,2.401-12.307,3.489-18.481
	c1.478-8.388,1.817-16.697-2.88-24.356c-7.497-12.22-22.835-16.017-36.201-9.026C71.208,42.571,70.513,43.59,68.979,43.058z'/>
<path class='darkBlue' fill='#084378' d='M55.759,95.332c-0.011,7.552-6.083,13.681-13.482,13.606
	c-7.439-0.075-13.42-6.256-13.339-13.785c0.08-7.382,6.019-13.315,13.353-13.339C49.878,81.79,55.771,87.704,55.759,95.332z'/>
<path class='lightBlue' fill='#65CDF5' d='M137.537,35.718c-0.014,7.774-5.676,13.395-13.447,13.35
	c-7.517-0.043-13.224-5.749-13.255-13.251c-0.033-7.726,5.682-13.523,13.351-13.545C131.919,22.25,137.55,27.92,137.537,35.718z'/>
<path fill='#084378' d='M301.597,99.837c0.769-1.914,1.411-3.515,2.126-5.298
	c3.139,0.707,5.965,2.944,9.228,1.716c1.063-0.877,1.12-1.832,0.609-2.926c-1.452-1.493-3.534-1.62-5.337-2.357
	c-4.431-1.811-6.223-4.635-5.547-8.882c0.587-3.687,3.674-6.254,7.927-6.592c4.405-0.351,7.488,0.743,10.798,3.858
	c-0.854,1.496-1.907,2.841-3.246,4.125c-2.268-0.673-4.103-2.749-6.754-2.27c-0.902,0.163-1.372,0.763-1.707,1.344
	c-0.084,2.01,1.266,2.498,2.588,3.006c1.408,0.542,2.856,0.993,4.223,1.624c3.55,1.639,4.984,4.086,4.658,7.75
	c-0.323,3.653-2.471,6.246-6.142,7.144C310.396,103.21,306.068,102.305,301.597,99.837z'/>
<path fill='#084378' d='M422.869,63.795l-1.852-5.491c-2.561,0-4.977,0-7.579,0
	c-0.606,1.706-1.28,3.707-1.915,5.491h-6.367c2.795-8.726,5.525-17.449,8.279-26.046c2.592,0,5.01,0,7.722,0
	c2.923,8.496,5.857,17.254,8.882,26.046H422.869z'/>
<path //*class='darkBlue'*// fill='#084378' d='M172.851,56.307c1.482,1.226,2.457,2.624,3.365,4.299
	c-3.872,3.612-8.31,4.528-13.255,3.052c-4.673-1.394-6.9-4.878-7.606-9.483c-0.789-5.153-0.327-10.085,3.618-13.946
	c5.153-5.044,12.988-4.042,17.607,2.144c-1.096,1.551-2.84,2.301-4.286,3.404c-0.236-0.093-0.476-0.121-0.616-0.253
	c-1.7-1.595-3.298-3.466-6.034-2.438c-2.748,1.032-3.177,3.527-3.514,5.96c-0.172,1.248-0.106,2.573,0.102,3.822
	c0.96,5.773,4.477,7.297,9.503,4.18C172.106,56.819,172.462,56.566,172.851,56.307z'/>
<path fill='#084378' d='M168.154,101.973c-2.504,0-4.547,0-6.792,0
	c0-6.645,0-13.136,0-19.699c-2.248-0.958-4.514-0.029-6.827-0.707c-0.395-1.724-0.264-3.53-0.055-5.56c6.875,0,13.682,0,20.621,0
	c0,1.886,0,3.609,0,5.501c-2.142,0.762-4.513-0.017-6.947,0.526C168.154,88.641,168.154,95.125,168.154,101.973z'/>
<path fill='#084378' d='M268.445,52.844c2.852,1.567,3.434,4.091,2.549,6.881
	s-3.303,3.79-6.004,3.913c-2.893,0.133-5.795,0.03-8.949,0.03c0-6.578,0-13.135,0-19.952c2.535-0.651,5.304-0.299,8.025-0.25
	c2.342,0.042,4.675,0.422,5.851,2.946C271.07,48.887,270.113,50.896,268.445,52.844z'/>
<polygon fill='#084378' points='549.199,43.52 554.142,43.52 554.142,51.142 
	560.66,51.142 560.66,43.52 565.689,43.52 565.689,63.698 560.66,63.698 560.66,55.944 554.142,55.944 554.142,63.698 
	549.199,63.698 549.199,43.52 	'/>
<path fill='#084378' d='M222.805,94.322c0,2.716,0,5.122,0,7.66c-1.859,0-3.431,0-5.187,0
	c0-6.716,0-13.289,0-20.181c2.687,0,5.22-0.043,7.751,0.012c2.744,0.061,5.448,0.362,6.72,3.337c1.29,3.019,0.133,5.421-2.1,7.393
	c1.368,3.115,2.706,6.162,4.092,9.321c-1.966,0.45-3.675,0.271-5.132,0.176C226.367,99.882,227.53,95.119,222.805,94.322z'/>
<path fill='#084378' d='M355.264,92.632c1.357,3.093,2.697,6.148,4.127,9.405
	c-1.96,0-3.577,0-5.195,0.001c-2.553-1.97-1.451-6.689-5.76-7.749c-0.764,2.583-0.041,5.028-0.531,7.534
	c-1.527,0.606-3.127,0.219-4.961,0.23c0-6.629,0-13.188,0-20.02c3.346-0.445,6.804-0.505,10.203,0.024
	C358.196,82.844,359.689,88.753,355.264,92.632z'/>
<path fill='#084378' d='M292.382,53.512c0.069,7.979-5.253,12.415-12.233,10.176
	c-0.654-0.211-1.313-0.483-1.894-0.848c-3.718-2.331-5.147-9.176-3.007-14.269c1.787-4.252,6.19-6.34,10.941-5.187
	C290.134,44.342,292.333,47.939,292.382,53.512z'/>
<path fill='#084378' d='M302.326,55.94c-1.05,1.733-0.705,2.923-0.761,4.06
	c-0.056,1.146-0.013,2.295-0.013,3.376c-1.748,0.658-3.253,0.327-4.991,0.328c0-6.684,0-13.339,0-20.259
	c2.75,0,5.407-0.126,8.048,0.037c2.588,0.159,5.297,0.583,6.32,3.459c0.981,2.761,0.3,5.284-2.109,7.258
	c1.306,3.064,2.605,6.112,4.018,9.426c-2.021,0-3.623,0-5.22,0C305.392,61.536,305.969,57.369,302.326,55.94z'/>
<path fill='#084378' d='M481.754,63.618c-2.422-2.183-1.439-6.638-5.939-7.56
	c0,2.651,0,4.924,0,7.215c-1.806,0.833-3.379,0.417-5.139,0.41c0-6.739,0-13.316,0-20.075c3.197-0.32,6.334-0.461,9.416,0.035
	c5.419,0.873,7.082,5.461,3.628,9.761c-0.253,0.315-0.872,0.338-1.001,0.383c1.586,3.642,2.873,6.602,4.279,9.83
	C485.008,63.618,483.406,63.618,481.754,63.618z'/>
<path fill='#084378' d='M325.741,101.914c0-6.646,0-13.137,0-19.88
	c4.22,0,8.446,0,12.822,0v3.974l-7.335,0v3.682l6.306,0v4.214l-6.306,0v3.741h7.335c0,1.353,0,2.696,0,4.269
	C334.383,101.914,330.393,101.914,325.741,101.914z'/>
<path fill='#084378' d='M426.666,100.388c0.559-1.613,0.861-2.965,1.775-4.032
	c5.949,1.862,5.949,1.862,7.701-0.133c-0.34-1.741-1.916-1.864-3.165-2.333c-4.7-1.762-6.468-4.271-5.43-7.845
	c1.022-3.52,4.708-5.295,9.068-4.441c1.996,0.391,3.712,1.248,5.25,2.591c-0.36,1.512-1.417,2.393-2.369,3.328
	c-4.538-1.792-4.538-1.792-6.603-0.479c0.037,1.855,1.605,1.998,2.838,2.531c1.273,0.55,2.625,0.996,3.771,1.749
	c2.069,1.36,2.66,3.472,2.226,5.815c-0.494,2.661-2.18,4.372-4.794,4.944C433.403,102.855,430.029,102.292,426.666,100.388z'/>
<path fill='#084378' d='M522.105,63.624l-1.355-4.06l-5.9,0.01
	c-0.509,1.292-1.021,2.71-1.549,4.05h-4.931c2.158-6.682,4.286-13.441,6.437-20.097c1.964,0,3.799,0,5.931,0
	c2.263,6.602,4.531,13.219,6.89,20.097C525.611,63.624,524.014,63.624,522.105,63.624z'/>
<path fill='#084378' d='M456.914,63.62c-1.706,0.182-3.198,0.313-4.873-0.12
	c0-6.565,0-13.136,0-19.939c3.123,0,6.141-0.256,9.096,0.064c3.85,0.416,5.805,2.697,5.896,6.151
	c0.096,3.572-1.963,6.155-5.596,6.93c-1.248,0.266-2.524,0.39-3.861,0.591C456.726,59.241,457.707,61.411,456.914,63.62z'/>
<path fill='#084378' d='M263.226,95.505c0,2.226,0,4.182,0,6.262
	c-1.761,0.639-3.356,0.275-5.149,0.307c0-6.67,0-13.254,0-20.215c2.938,0,5.93-0.169,8.896,0.043
	c3.713,0.266,5.87,2.592,6.022,6.04c0.154,3.519-1.979,6.343-5.474,7.089C266.285,95.296,265,95.319,263.226,95.505z'/>
<path fill='#084378' d='M360.619,43.54c1.902,0,3.5,0,5.302,0
	c1.286,4.329,2.558,8.608,4.084,13.749c1.554-5.174,2.833-9.437,4.105-13.674c1.632-0.295,3.118-0.302,5.087,0.047
	c-2.166,6.66-4.297,13.208-6.446,19.815c-1.934,0.561-3.751,0.166-5.675,0.299C364.902,56.965,362.793,50.354,360.619,43.54z'/>
<path fill='#084378' d='M372.031,102.049c-1.883,0-3.506,0-5.545,0
	c-2.112-6.572-4.259-13.252-6.431-20.01c1.866-0.386,3.473-0.324,5.306-0.066c1.259,4.263,2.531,8.569,4.023,13.622
	c1.509-5.031,2.789-9.299,4.067-13.563c1.571-0.4,3.063-0.286,5.122-0.062C376.359,88.769,374.195,95.408,372.031,102.049z'/>
<path fill='#084378' d='M438.649,57.402c0,2.039,0,3.984,0,5.997
	c-1.751,0.629-3.307,0.337-5.028,0.275c0-6.759,0-13.319,0-20.081c3.31-0.238,6.529-0.495,9.709,0.172
	c3.498,0.732,5.025,2.656,5.08,6.031c0.057,3.466-1.625,5.934-4.805,6.775C442.06,56.981,440.439,57.111,438.649,57.402z'/>
<path fill='#084378' d='M404.873,96.324c1.048,1.131,1.853,1.96,2.24,3.073
	c-3.74,3.904-10.166,4.091-13.616,0.421c-3.878-4.124-3.36-13.019,0.971-16.476c3.885-3.103,10.659-2.618,13.05,2.189
	c-0.74,1.198-2.073,1.699-3.308,2.647c-1.905-1.581-3.895-3.749-6.472-1.12c-1.806,1.842-1.887,7.507-0.115,9.552
	C399.982,99.335,402.411,97.691,404.873,96.324z'/>
<path fill='#084378' d='M545.884,47.454c-1.244,0.899-2.27,1.639-3.351,2.42
	c-1.965-1.485-3.786-3.697-6.368-1.308c-1.834,1.698-2.03,7.272-0.425,9.571c1.445,2.069,3.244,2.083,7.551-0.012
	c0.759,0.964,1.537,1.953,2.29,2.91c-3.146,4.287-10.921,3.969-14.031,0.282c-3.554-4.212-2.953-12.887,1.157-16.276
	C535.877,42.427,543.27,42.048,545.884,47.454z'/>
<path fill='#084378' d='M274.668,81.933c1.959,0,3.66,0,5.599,0
	c1.032,2.053,2.112,4.204,3.488,6.943c1.378-2.576,2.534-4.737,3.737-6.986c1.602,0,3.218,0,5.221,0
	c-1.964,4.771-6.475,13.429-6.475,13.429v6.702h-5.031v-6.702C281.207,95.319,276.648,86.599,274.668,81.933z'/>
<path fill='#084378' d='M201.631,43.752c1.625-0.604,3.127-0.26,4.99-0.291
	c0,5.076,0,10.135,0,15.579c2.84,0,5.331,0,7.777,0c0.689,1.709,0.324,3.063,0.395,4.576c-4.481,0-8.736,0-13.162,0
	C201.631,56.897,201.631,50.321,201.631,43.752z'/>
<path fill='#084378' d='M223.547,43.52c0,5.255,0,10.233,0,15.565
	c2.718,0,5.214,0,7.617,0c0.689,1.685,0.545,3.028,0.202,4.532c-4.278,0-8.536,0-12.921,0c0-6.75,0-13.333,0-19.911
	C220.095,43.205,221.588,43.435,223.547,43.52z'/>
<path class='darkBlue' fill='#084378' d='M109.173,110.105c0.117,0.36,0.229,0.568,0.25,0.785
	c0.743,7.771,0.742,7.771-6.702,10.238c-0.882,0.293-1.762,0.589-2.643,0.885c-6.837,2.296-6.927,2.257-10.333-4.799
	C96.224,114.806,102.987,113.241,109.173,110.105z'/>
<path fill='#084378' d='M343.412,63.636c-1.937,0-3.396,0-5.148,0
	c0-5.071,0-10.019,0-14.853c-1.734-1.186-3.449-0.245-5.194-0.898c-0.684-1.211-0.294-2.668-0.192-4.292c5.275,0,10.447,0,15.672,0
	c0.57,1.399,0.322,2.756,0.187,4.151c-1.616,0.91-3.454-0.022-5.323,0.724C343.412,53.343,343.412,58.303,343.412,63.636z'/>
<path fill='#084378' d='M357.223,63.542c-1.57,0.374-3.041,0.283-4.714,0.054
	c0-6.61,0-13.193,0-19.934c1.472-0.442,2.944-0.232,4.714-0.158C357.223,50.125,357.223,56.692,357.223,63.542z'/>
<path fill='#084378' d='M386.728,101.939c-1.738,0.263-3.2,0.313-4.837-0.021
	c0-6.591,0-13.144,0-19.892c1.531-0.467,3.086-0.195,4.837-0.178C386.728,88.601,386.728,95.15,386.728,101.939z'/>
<path class='lightBlue' fill='#65CDF5' d='M71.977,82.623c-9.979-5.078-10.667-20.5-6.099-28.827
	C65.647,64.166,67.031,73.505,71.977,82.623z'/>
<path class='darkBlue' fill='#084378' d='M98.609,124.757c2.86-1.052,5.273-2.057,8.112-2.462
	c0.177,4.313-3.528,3.202-5.188,4.329C100.135,126.612,99.668,125.742,98.609,124.757z'/>
<polygon fill='#FFFFFF' points='419.752,53.042 414.59,53.042 417.172,44.362 	'/>
<path fill='#FFFFFF' d='M261.279,55.403c1.597-0.196,2.854-0.36,3.938,0.563
	c0.664,0.962,0.584,1.937,0.096,2.91c-1.104,0.949-2.36,0.847-3.719,0.682C260.828,58.306,261.213,56.963,261.279,55.403z'/>
<path fill='#FFFFFF' d='M261.229,51.157c0-1.246,0-2.263,0-3.295
	c1.162-0.757,2.199-0.492,3.18,0.033c0.715,0.9,0.639,1.762,0.175,2.7C263.671,51.305,262.68,51.771,261.229,51.157z'/>
<path fill='#FFFFFF' d='M226.561,89.727c-1.233,0.804-2.337,0.6-3.585,0.512
	c0-1.376,0-2.603,0-4.081c1.181-0.343,2.34-0.234,3.432,0.183C227.346,87.47,227.259,88.522,226.561,89.727z'/>
<path fill='#FFFFFF' d='M351.775,86.417c0.834,1.307,0.734,2.348-0.109,3.385
	c-0.992,0.706-2.104,0.604-3.291,0.383c-0.479-1.329-0.4-2.648-0.066-4.005C349.486,85.804,350.572,85.912,351.775,86.417z'/>
<path fill='#FFFFFF' d='M190.733,59.142c-3.683,0.755-5.038,0.008-5.582-3.028
	c-0.328-1.829-0.408-3.729,0.129-5.53c0.479-1.604,1.321-3.037,3.334-3.013c1.832,0.023,2.748,1.302,3.117,2.804
	C192.462,53.343,192.588,56.323,190.733,59.142z'/>
<path fill='#FFFFFF' d='M287.113,53.676c0.039,3.625-1.246,5.862-3.447,6.001
	c-2.433,0.154-3.904-2.014-3.987-5.873c-0.079-3.707,1.368-6.192,3.634-6.239C285.596,47.519,287.07,49.89,287.113,53.676z'/>
<path fill='#FFFFFF' d='M501.423,53.844c-0.123,1.24-0.228,2.324-0.608,3.436
	c-0.504,1.463-1.418,2.393-3.037,2.397c-1.773,0.006-2.787-1.04-3.248-2.625c-0.591-2.036-0.744-4.098-0.173-6.192
	c0.456-1.673,1.151-3.132,3.097-3.264c2.18-0.149,3.045,1.366,3.496,3.182C501.2,51.782,501.423,52.717,501.423,53.844z'/>
<path fill='#FFFFFF' d='M305.258,51.542c-1.188,0.523-2.256,0.964-3.439,0.246
	c-0.604-1.228-0.137-2.488-0.271-3.697c1.386-0.916,2.541-0.398,3.453-0.138C306.313,49.192,306.087,50.253,305.258,51.542z'/>
<path fill='#FFFFFF' d='M475.594,47.756c1.728-0.222,2.975-0.35,4.014,0.625
	c0.629,0.99,0.4,1.96-0.052,2.948c-1.069,0.791-2.228,1.048-3.553,0.59C475.244,50.632,475.846,49.289,475.594,47.756z'/>
<path fill='#FFFFFF' d='M244.757,86.939c1.092,2.321,1.581,4.333,1.973,6.428
	c-1.104,0.804-2.233,0.259-3.536,0.467C243.199,91.353,244.395,89.454,244.757,86.939z'/>
<polygon fill='#FFFFFF' points='517.924,49.113 519.565,55.395 515.945,55.395 	'/>
<path fill='#FFFFFF' d='M241.12,55.462c0.025-2.284,1.06-4.119,1.548-6.483
	c1.332,1.965,1.357,3.995,2.051,5.925C243.607,55.988,242.404,55.482,241.12,55.462z'/>
<path fill='#FFFFFF' d='M323.895,48.884c0.682,2.452,1.209,4.349,1.738,6.253
	c-1.197,0.716-2.297,0.468-3.637,0.251C322.131,53.242,322.762,51.305,323.895,48.884z'/>
<path fill='#FFFFFF' d='M461.2,51.77c-1.067,1.081-2.181,1.228-3.542,1.213
	c-0.732-1.642-0.338-3.319-0.332-5.21c1.489-0.271,2.752-0.269,3.797,0.887C461.697,49.666,461.639,50.661,461.2,51.77z'/>
<path fill='#FFFFFF' d='M266.629,90.658c-1.025,0.445-1.92,0.59-3.02,0.332
	c-0.799-1.438-0.293-3.019-0.34-4.546c1.234-0.761,2.303-0.463,3.335-0.019C267.871,87.788,267.871,89.133,266.629,90.658z'/>
<path fill='#FFFFFF' d='M442.332,48.284c0.906,1.28,0.92,2.453,0.152,3.735
	c-0.938,0.82-2.078,1.161-3.549,0.787c-0.553-1.605-0.348-3.255-0.149-4.875C440.063,47.375,441.129,47.581,442.332,48.284z'/>
<path fill='#084378' d='M410.76,101.955c0-6.646,0-13.137,0-19.88
	c4.219,0,8.445,0,12.822,0v3.974l-7.335,0v3.682l6.306,0v4.214l-6.306,0v3.741h7.335c0,1.353,0,2.696,0,4.269
	C419.401,101.955,415.412,101.955,410.76,101.955z'/>
<path fill='#084378' d='M200.762,101.912c0-6.646,0-13.137,0-19.879
	c4.219,0,8.446,0,12.822,0v3.973l-7.335,0v3.682h6.306v4.214h-6.306v3.741h7.335c0,1.353,0,2.696,0,4.268
	C209.404,101.912,205.414,101.912,200.762,101.912z'/>
<polygon fill='#084378' points='179.404,81.881 184.346,81.881 184.346,89.504 
	190.865,89.503 190.865,81.881 195.894,81.881 195.894,102.06 190.865,102.06 190.865,94.306 184.346,94.306 184.346,102.06 
	179.404,102.06 179.404,81.881 '/>
<path fill='#084378' d='M249.355,102.021l-1.356-4.059l-5.899,0.009
	c-0.51,1.293-1.021,2.71-1.549,4.05h-4.931c2.158-6.682,4.287-13.441,6.436-20.097c1.964,0,3.8,0,5.931,0
	c2.264,6.603,4.531,13.219,6.89,20.097C252.861,102.021,251.264,102.021,249.355,102.021z'/>
<polygon fill='#FFFFFF' points='245.173,87.51 246.814,93.792 243.195,93.792 '/>
<path fill='#084378' d='M247.287,63.643l-1.355-4.059l-5.9,0.01
	c-0.508,1.292-1.021,2.709-1.548,4.049h-4.931c2.158-6.682,4.286-13.441,6.436-20.097c1.964,0,3.799,0,5.932,0
	c2.263,6.602,4.531,13.218,6.889,20.097C250.793,63.643,249.196,63.643,247.287,63.643z'/>
<polygon fill='#FFFFFF' points='243.105,49.132 244.748,55.415 241.128,55.415 '/>
<path fill='#084378' d='M328.133,63.641l-1.356-4.059l-5.899,0.009
	c-0.51,1.293-1.021,2.71-1.549,4.05h-4.932c2.158-6.682,4.287-13.441,6.437-20.097c1.964,0,3.8,0,5.931,0
	c2.264,6.603,4.531,13.219,6.89,20.097C331.639,63.641,330.041,63.641,328.133,63.641z'/>
<polygon fill='#FFFFFF' points='323.95,49.13 325.592,55.413 321.973,55.413 '/>
<path fill='#084378' d='M382.642,63.484c0-6.646,0-13.137,0-19.879
	c4.22,0,8.446,0,12.822,0v3.974h-7.335v3.682h6.306v4.214h-6.306v3.741h7.335c0,1.353,0,2.696,0,4.268
	C391.283,63.484,387.293,63.484,382.642,63.484z'/>
<path fill='#084378' d='M197.583,53.605c0.07,7.979-5.253,12.415-12.233,10.176
	c-0.656-0.21-1.313-0.483-1.894-0.848c-3.718-2.331-5.147-9.176-3.007-14.269c1.787-4.253,6.19-6.34,10.942-5.187
	C195.334,44.435,197.534,48.032,197.583,53.605z'/>
<path fill='#FFFFFF' d='M192.314,53.769c0.04,3.625-1.246,5.862-3.447,6.001
	c-2.433,0.154-3.903-2.014-3.987-5.874c-0.08-3.707,1.368-6.192,3.634-6.238C190.797,47.611,192.271,49.983,192.314,53.769z'/>
<path fill='#084378' d='M506.777,53.475c0.07,7.979-5.254,12.415-12.232,10.176
	c-0.656-0.21-1.313-0.483-1.895-0.848c-3.719-2.331-5.146-9.176-3.008-14.269c1.787-4.253,6.191-6.34,10.943-5.187
	C504.529,44.304,506.729,47.901,506.777,53.475z'/>
<path fill='#FFFFFF' d='M501.508,53.638c0.04,3.625-1.245,5.862-3.447,6.001
	c-2.432,0.154-3.902-2.014-3.986-5.874c-0.079-3.707,1.368-6.192,3.635-6.238C499.992,47.48,501.466,49.852,501.508,53.638z'/>
</svg></div>
        <style>
        @keyframes colorChangeDarkLight {
            0% {
                fill: #084378;
            }
            70% {
                fill: #084378;
            }
            100% {
                fill: #65CDF5;
            }
        }
        @keyframes colorChangeLightDark {
            0% {
                fill: #65CDF5;
            }
            70% {
                fill: #65CDF5;
            }
            100% {
                fill: #084378;
            }
        }
        #logoSVG .darkBlue {
            animation: colorChangeDarkLight 5s ease 0s infinite alternate;
        }
        #logoSVG .lightBlue {
            animation: colorChangeLightDark 5s ease 0s infinite alternate;
        }
        </style>
        <form style='margin:auto;border:1px solid gray; width:33%; padding: 10px; padding-top: 0px; border-radius:10px; background-color:#65CDF5; margin-top:10em;' method='post'>"
         ."<h1 style=\"text-align:center; font-family: 'Lato', sans-serif; font-weight: 300; font-size: 30pt\">Login to CATS</h1>"
         ."<input type='hidden' name='timezone' id='timezone'>"
         ."<input type='hidden' name='login' vaue='true'>"
         ."<input type='text' placeholder='Username' style='display:block; font-family: \"Lato\", sans-serif; font-weight: 400; margin:auto; border-radius:5px; border-style: inset outset outset inset; background-color:#87d8f7;' name='seedsession_uid' />"
         ."<br />"
         ."<input type='password' placeholder='Password' style='display:block; font-family: \"Lato\", sans-serif; font-weight: 400; margin:auto; border-radius:5px; border-style: inset outset outset inset; background-color:#87d8f7;' name='seedsession_pwd' />"
         ."<br />"
         ."<input type='submit' value='Login' style='border-style: inset outset outset inset; font-family: \"Lato\", sans-serif; font-weight: 400; background-color:#87d8f7; border-radius:5px; display:block; margin:auto;' />"
         ."</form>"
         ."<div style='margin:auto; width:33%; padding: 10px; padding-top: 0px;'><a style='margin:auto' href='./passwordReset.php'>Reset Password</a></div>"
         ."<script>
            var timezone_offset_minutes = new Date().getTimezoneOffset();
	        timezone_offset_minutes = timezone_offset_minutes == 0 ? 0 : -timezone_offset_minutes;
            document.getElementById('timezone').value = timezone_offset_minutes;
           </script>";

    // This is where we store the user's current screen. If they have logged out, or the login expired, reset their screen to the default.
    $oApp->sess->VarSet( 'screen', "" );

    exit;
}

/* UI and Command Paradigm:

   The session variable 'screen' controls which screen you see next. To move to a different screen, issue http parm "screen=foo".

   Commands are processed before screens are drawn. Issue "cmd=bar" to make something happen.

   screen names and cmd names have three parts:  perm level name

          perm is a SEEDSessionPerms perm (we use therapist, leader, admin, etc)
          level is one, two, or three hyphens:
              -    = read access required
              --   = write access required
              ---  = admin access required
          name is the name of the screen or command

          e.g. the screen called therapist-calendar requires read access on the "therapist" perm
               the screen called therapist--editclients requires write access on the "therapist" perm
               the screen called admin-showpermissions requires read access on the "admin" perm

               the command called client-appt requires read access on the "client" perm
               the command called leader--addusers requires write access on the "leader" perm
               the command called calendar---addcalendar requires admin access on the "calendar" perm
*/

//Configure timezone
if(is_numeric($oApp->sess->SmartGPC('timezone'))){
    $oApp->sess->VarSet('timezone', timezone_name_from_abbr("", $oApp->sess->SmartGPC('timezone')*60, false));
}

if($oApp->sess->VarGet('timezone')){
    date_default_timezone_set($oApp->sess->VarGet('timezone'));
}

if(SEEDInput_Get("login") && !$oApp->sess->SmartGPC("screen")){
    header("HTTP/1.1 303 SEE OTHER");
    header("Location: ".CATSDIR."home");
    exit();
}

$oUI = new CATS_MainUI( $oApp );

//var_dump($_REQUEST);
//var_dump($_SESSION);

$s = "";

$s = $oUI->Screen();

echo $oUI->OutputPage( $s );

?>