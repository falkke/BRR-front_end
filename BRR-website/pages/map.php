<?php
	require "initialPos.php";
	
	if( (isset($_GET['runner']) && !empty($_GET['runner']) && does_runner_exist($_GET['runner']))
	&&	(isset($_GET['race']) && !empty($_GET['race']) && does_race_exist($_GET['race']) && is_current_race($race_id))) {
		$runner_id = $_GET['runner'];
		$race_id = $_GET['race'];
		$runner = get_runner($runner_id);
		$race = get_race($race_id);
		$race_runner = get_race_runner($runner_id, $race_id);
		$class = get_race_runner_class($runner_id, $race->ID);
		$club = get_race_runner_team($runner_id, $race->ID);
	}
	
	else {
		header("Location:index.php?page=home");
	}
?>

<main role="main" class="container no-gutters">
	<div class="row">
		<div class="nav-side-menu">
			<div class="brand"><?=$runner->FirstName?> <?=$runner->LastName?></div>
			<div class="runner-info">
				<ul>
					<li><?=$runner->Gender?></li>
					<li><?=$runner->DateOfBirth?></li>
				</ul>
			</div>
			
			<?php 
				if($race != "") {
					?>
						<div class="brand"><?=$race->Name ?></div>
						<div class="runner-race-info">
							<ul>
								<li>Place : <?=$race_runner->Place?></li>
								<li>Bib : <?=$race_runner->Bib?></li>
								<li>Team : <?=$club->Name?></li>
							</ul>
						</div>
				
					<?php
					if(sizeof(get_races_runner($runner_id)) > 1) {
						?>
							<div class="brand">History</div>
							
							<span class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></span>
							<div class="menu-list">
								<ul id="menu-content" class="menu-content collapse out">
									<li data-toggle="collapse" data-target="#truc" class="collapsed">
										<a href="#">Other Races</a>
									</li>
									
									<ul class="sub-menu collapse" id="truc">
										<?php
											foreach(get_races_runner($runner_id) as $other_race_runner) {
												$other_race = get_race(get_race_from_instance($other_race_runner->RaceInstance)->Race);
												if($other_race->ID != $race->ID){
													?>		
														<li><a href="index.php?page=runner&runner=<?=$runner_id ?>&race=<?=$other_race->ID ?>"><?=$other_race->Name ?></a></li>
													<?php
												}
											}
										?>
									</ul>
								</ul>
							</div>
						<?php 
					}
				}
			?>
		</div>
	</div>
		
	<div class="section-template">
		<a class="link-title" href="index.php?page=race&race=<?=$race->ID?>&gender=<?=$class->Gender?>&distance=<?=$class->Distance?>"><h2 class="page-title followed-title"><?= $race->Name ?></h2></a>
		<h3 class="page-subtitle"><?= "Tracking " . $runner->FirstName . " " . $runner->LastName ?></h3>
		
		<a title="Timestamp" class="bg-primary text-white table-button" href="index.php?page=runner&race=<?=$race->ID?>&runner=<?=$runner_id?>">&#128441</a>
			
		<h2 class="page-title followed-title">Tracking</h2>
		
		<div id="map"></div>
		
		<script>
			var RacePath;
			var cityCircle;
			var map;
			var currentPos = <?php echo $max ?>;
			var response2 = 0;
			var marker;

			var RaceCoordinates = [
				{lat: 59.6390965,lng: 16.5230476},{lat: 59.6393300,lng: 16.5230000},{lat: 59.6394695,lng: 16.5231278},
				{lat: 59.6398441,lng: 16.5239099},{lat: 59.6400224,lng: 16.5240132},{lat: 59.6402483,lng: 16.5241188},
				{lat: 59.6407431,lng: 16.5237844},{lat: 59.6408759,lng: 16.5238796},{lat: 59.6410086,lng: 16.5239435},
				{lat: 59.6413440,lng: 16.5240323},{lat: 59.6424695,lng: 16.5240040},{lat: 59.6425116,lng: 16.5240017},
				{lat: 59.6429062,lng: 16.5239694},{lat: 59.6434059,lng: 16.5232451},{lat: 59.6440385,lng: 16.5226520},
				{lat: 59.6445911,lng: 16.5221465},{lat: 59.6452321,lng: 16.5218139},{lat: 59.6455393,lng: 16.5218022},
				{lat: 59.6458710,lng: 16.5218812},{lat: 59.6462667,lng: 16.5217104},{lat: 59.6466188,lng: 16.5214966},
				{lat: 59.6467559,lng: 16.5211865},{lat: 59.6468547,lng: 16.5209724},{lat: 59.6470791,lng: 16.5207276},
				{lat: 59.6472262,lng: 16.5207242},{lat: 59.6474919,lng: 16.5205244},{lat: 59.6476758,lng: 16.5204888},
				{lat: 59.6481793,lng: 16.5207798},{lat: 59.6482468,lng: 16.5207966},{lat: 59.6484808,lng: 16.5206150},
				{lat: 59.6485928,lng: 16.5205362},{lat: 59.6489453,lng: 16.5204484},{lat: 59.6497831,lng: 16.5203300},
				{lat: 59.6499841,lng: 16.5202647},{lat: 59.6504338,lng: 16.5198777},{lat: 59.6506360,lng: 16.5198501},
				{lat: 59.6507774,lng: 16.5198175},{lat: 59.6509988,lng: 16.5198116},{lat: 59.6512053,lng: 16.5198764},
				{lat: 59.6514955,lng: 16.5198761},{lat: 59.6517756,lng: 16.5199985},{lat: 59.6519061,lng: 16.5202540},
				{lat: 59.6523643,lng: 16.5209753},{lat: 59.6528111,lng: 16.5216005},{lat: 59.6530642,lng: 16.5215040},
				{lat: 59.6531610,lng: 16.5213597},{lat: 59.6533470,lng: 16.5210995},{lat: 59.6535383,lng: 16.5209605},
				{lat: 59.6537359,lng: 16.5205478},{lat: 59.6539440,lng: 16.5203542},{lat: 59.6540840,lng: 16.5201651},
				{lat: 59.6543727,lng: 16.5199119},{lat: 59.6545047,lng: 16.5197668},{lat: 59.6548434,lng: 16.5192005},
				{lat: 59.6549628,lng: 16.5190401},{lat: 59.6552093,lng: 16.5189907},{lat: 59.6555268,lng: 16.5187831},
				{lat: 59.6556899,lng: 16.5187303},{lat: 59.6558553,lng: 16.5185430},
				{lat: 59.6562884,lng: 16.5181900},{lat: 59.6563683,lng: 16.5179524},{lat: 59.6568704,lng: 16.5170615},
				{lat: 59.6571176,lng: 16.5169040},{lat: 59.6579474,lng: 16.5162521},{lat: 59.6582317,lng: 16.5161299},
				{lat: 59.6585834,lng: 16.5162196},{lat: 59.6588556,lng: 16.5161755},{lat: 59.6589885,lng: 16.5159597},
				{lat: 59.6592762,lng: 16.5155658},{lat: 59.6594412,lng: 16.5154468},{lat: 59.6596278,lng: 16.5153523},
				{lat: 59.6598567,lng: 16.5155111},{lat: 59.6600254,lng: 16.5157437},{lat: 59.6602819,lng: 16.5158237},
				{lat: 59.6604248,lng: 16.5156546},{lat: 59.6606548,lng: 16.5157253},{lat: 59.6608447,lng: 16.5159101},
				{lat: 59.6609655,lng: 16.5161950},{lat: 59.6611757,lng: 16.5165670},{lat: 59.6613794,lng: 16.5167469},
				{lat: 59.6615496,lng: 16.5170478},{lat: 59.6614384,lng: 16.5180256},
				{lat: 59.6614985,lng: 16.5185660},{lat: 59.6615671,lng: 16.5189687},{lat: 59.6617524,lng: 16.5189918},
				{lat: 59.6619855,lng: 16.5189030},{lat: 59.6621755,lng: 16.5186748},{lat: 59.6623174,lng: 16.5185024},
				{lat: 59.6625173,lng: 16.5186410},{lat: 59.6626066,lng: 16.5187809},{lat: 59.6626966,lng: 16.5189609},
				{lat: 59.6630620,lng: 16.5194547},{lat: 59.6632202,lng: 16.5199550},{lat: 59.6633426,lng: 16.5201252},
				{lat: 59.6635547,lng: 16.5202527},{lat: 59.6637244,lng: 16.5206337},{lat: 59.6641275,lng: 16.5211545},
				{lat: 59.6643193,lng: 16.5212505},{lat: 59.6645610,lng: 16.5216357},{lat: 59.6647578,lng: 16.5214723},
				{lat: 59.6649168,lng: 16.5212694},{lat: 59.6650670,lng: 16.5209252},{lat: 59.6654621,lng: 16.5206683},
				{lat: 59.6656184,lng: 16.5203063},{lat: 59.6658057,lng: 16.5200650},{lat: 59.6657839,lng: 16.5196698},
				{lat: 59.6656750,lng: 16.5192691},{lat: 59.6656580,lng: 16.5188552},{lat: 59.6655629,lng: 16.5184241},
				{lat: 59.6654230,lng: 16.5181519},{lat: 59.6653888,lng: 16.5177839},{lat: 59.6657896,lng: 16.5176320},
				{lat: 59.6660185,lng: 16.5175820},{lat: 59.6662854,lng: 16.5168374},
				{lat: 59.6663696,lng: 16.5163731},{lat: 59.6664126,lng: 16.5159226},
				{lat: 59.6663921,lng: 16.5154911},{lat: 59.6663187,lng: 16.5151562},{lat: 59.6663587,lng: 16.5147225},
				{lat: 59.6662255,lng: 16.5143997},{lat: 59.6662055,lng: 16.5139549},{lat: 59.6661471,lng: 16.5136272},
				{lat: 59.6659803,lng: 16.5133051},{lat: 59.6658900,lng: 16.5127856},{lat: 59.6657561,lng: 16.5123502},
				{lat: 59.6658163,lng: 16.5120890},{lat: 59.6658563,lng: 16.5118890},{lat: 59.6658563,lng: 16.5113990},
				{lat: 59.6659649,lng: 16.5111190},{lat: 59.6660854,lng: 16.5106933},{lat: 59.6661354,lng: 16.5103033},
				{lat: 59.6658060,lng: 16.5098070},{lat: 59.6653660,lng: 16.5095270},
				{lat: 59.6650290,lng: 16.5089977},{lat: 59.6648990,lng: 16.5087277},{lat: 59.6646794,lng: 16.5085230},
				{lat: 59.6644988,lng: 16.5086053},{lat: 59.6642505,lng: 16.5080862},{lat: 59.6636005,lng: 16.5060962},
				{lat: 59.6633792,lng: 16.5055265},
				{lat: 59.6627976,lng: 16.5050035},{lat: 59.6624672,lng: 16.5048694},{lat: 59.6618953,lng: 16.5049621},
				{lat: 59.6610939,lng: 16.5045196},{lat: 59.6608124,lng: 16.5042804},{lat: 59.6606423,lng: 16.5040612},
				{lat: 59.6602457,lng: 16.5033266},{lat: 59.6592650,lng: 16.5010797},{lat: 59.6590239,lng: 16.5007152},
				{lat: 59.6588377,lng: 16.5003940},{lat: 59.6585530,lng: 16.5000080},{lat: 59.6584130,lng: 16.4995673},
				{lat: 59.6583062,lng: 16.4991763},{lat: 59.6581541,lng: 16.4990766},{lat: 59.6571991,lng: 16.4990078},
				{lat: 59.6570380,lng: 16.4986487},{lat: 59.6552580,lng: 16.4939955},{lat: 59.6550280,lng: 16.4936974},
				{lat: 59.6553438,lng: 16.4932643},{lat: 59.6560517,lng: 16.4923781},{lat: 59.6561172,lng: 16.4922669},
				{lat: 59.6560947,lng: 16.4921747},{lat: 59.6560522,lng: 16.4915200},{lat: 59.6559577,lng: 16.4910056},
				{lat: 59.6556966,lng: 16.4905913},{lat: 59.6555727,lng: 16.4904887},{lat: 59.6554114,lng: 16.4905256},
				{lat: 59.6552027,lng: 16.4906966},{lat: 59.6550495,lng: 16.4907524},{lat: 59.6544593,lng: 16.4907535},
				{lat: 59.6540145,lng: 16.4906695},{lat: 59.6538909,lng: 16.4904857},{lat: 59.6538009,lng: 16.4902209},
				{lat: 59.6535900,lng: 16.4900805},{lat: 59.6530935,lng: 16.4892501},{lat: 59.6529115,lng: 16.4890555},
				{lat: 59.6528515,lng: 16.4887993},{lat: 59.6528015,lng: 16.4887493},{lat: 59.6527015,lng: 16.4887593},
				{lat: 59.6526015,lng: 16.4888893},{lat: 59.6525076,lng: 16.4889593},{lat: 59.6524576,lng: 16.4889593},
				{lat: 59.6523596,lng: 16.4889299},{lat: 59.6522896,lng: 16.4889200},{lat: 59.6522096,lng: 16.4888000},
				{lat: 59.6521096,lng: 16.4886000},{lat: 59.6520096,lng: 16.4884000},{lat: 59.6520096,lng: 16.4884020},
				{lat: 59.6519096,lng: 16.4882901},{lat: 59.6518096,lng: 16.4882001},{lat: 59.6517596,lng: 16.4881001},
				{lat: 59.6517496,lng: 16.4875901},{lat: 59.6517386,lng: 16.4874801},{lat: 59.6517086,lng: 16.4873601},
				{lat: 59.6516886,lng: 16.4873001},
				{lat: 59.6510203,lng: 16.4874588},{lat: 59.6506016,lng: 16.4877073},{lat: 59.6503718,lng: 16.4879907},
				{lat: 59.6502642,lng: 16.4884117},{lat: 59.6502502,lng: 16.4888333},{lat: 59.6502702,lng: 16.4893933},
				{lat: 59.6502812,lng: 16.4896333},{lat: 59.6502700,lng: 16.4899333},{lat: 59.6502001,lng: 16.4901593},
				{lat: 59.6498589,lng: 16.4904793},{lat: 59.6495932,lng: 16.4906593},{lat: 59.6494212,lng: 16.4907511},
				{lat: 59.6491694,lng: 16.4906570},{lat: 59.6488585,lng: 16.4904032},{lat: 59.6487585,lng: 16.4901596},
				{lat: 59.6485144,lng: 16.4889755},{lat: 59.6484289,lng: 16.4884922},{lat: 59.6483718,lng: 16.4882514},
				{lat: 59.6483370,lng: 16.4880914},{lat: 59.6482170,lng: 16.4878214},{lat: 59.6476170,lng: 16.4870214},
				{lat: 59.6473170,lng: 16.4869214},{lat: 59.6472170,lng: 16.4869214},{lat: 59.6471170,lng: 16.4870114},
				{lat: 59.6466170,lng: 16.4878114},{lat: 59.6465170,lng: 16.4882114},{lat: 59.6465190,lng: 16.4888914},
				{lat: 59.6465520,lng: 16.4892060},{lat: 59.6465020,lng: 16.4896932},{lat: 59.6453920,lng: 16.4925032},
				{lat: 59.6452320,lng: 16.4929565},{lat: 59.6450993,lng: 16.4932865},{lat: 59.6450093,lng: 16.4936865},
				{lat: 59.6450059,lng: 16.4941565},{lat: 59.6450959,lng: 16.4944565},{lat: 59.6452959,lng: 16.4947565},
				{lat: 59.6453959,lng: 16.4948565},{lat: 59.6454959,lng: 16.4951065},{lat: 59.6454559,lng: 16.4954865},
				{lat: 59.6451559,lng: 16.4955265},{lat: 59.6449559,lng: 16.4953265},{lat: 59.6447989,lng: 16.4950265},
				{lat: 59.6446589,lng: 16.4944265},{lat: 59.6445589,lng: 16.4943265},{lat: 59.6435589,lng: 16.4953565},
				{lat: 59.6430589,lng: 16.4959965},{lat: 59.6433589,lng: 16.4969965},{lat: 59.6425589,lng: 16.4977965},
				{lat: 59.6424289,lng: 16.4979965},{lat: 59.6423289,lng: 16.4980165},{lat: 59.6415089,lng: 16.4988165},
				{lat: 59.6414229,lng: 16.4983565},{lat: 59.6414029,lng: 16.4983465},{lat: 59.6413529,lng: 16.4984065},
				{lat: 59.6412529,lng: 16.4985665},{lat: 59.6411029,lng: 16.4988365},{lat: 59.6409029,lng: 16.4991265},
				{lat: 59.6407029,lng: 16.4993265},{lat: 59.6405029,lng: 16.4995065},{lat: 59.6402029,lng: 16.4997265},
				{lat: 59.6400029,lng: 16.4998965},{lat: 59.6396829,lng: 16.5001265},{lat: 59.6395829,lng: 16.5001900},
				{lat: 59.6393829,lng: 16.5003980},{lat: 59.6390829,lng: 16.5007480},{lat: 59.6384829,lng: 16.5014080},
				{lat: 59.6382829,lng: 16.5015480},{lat: 59.6380829,lng: 16.5016250},{lat: 59.6378829,lng: 16.5016980},
				{lat: 59.6375829,lng: 16.5016980},{lat: 59.6373829,lng: 16.5016980},{lat: 59.6371829,lng: 16.5016580},
				{lat: 59.6369829,lng: 16.5015980},{lat: 59.6367952,lng: 16.5015980},{lat: 59.6365952,lng: 16.5016980},
				{lat: 59.6364952,lng: 16.5017580},{lat: 59.6361952,lng: 16.5021580},{lat: 59.6353952,lng: 16.5034280},
				{lat: 59.6351952,lng: 16.5037652},{lat: 59.6349952,lng: 16.5041977},{lat: 59.6346952,lng: 16.5048577},
				{lat: 59.6343952,lng: 16.5052577},{lat: 59.6335952,lng: 16.5060135},{lat: 59.6333952,lng: 16.5062935},
				{lat: 59.6332952,lng: 16.5064835},{lat: 59.6331952,lng: 16.5066735},{lat: 59.6330952,lng: 16.5070035},
				{lat: 59.6331352,lng: 16.5072035},{lat: 59.6331852,lng: 16.5073435},{lat: 59.6332752,lng: 16.5075435},
				{lat: 59.6333100,lng: 16.5077035},{lat: 59.6333151,lng: 16.5078935},{lat: 59.6333051,lng: 16.5079935},
				{lat: 59.6332951,lng: 16.5082935},{lat: 59.6332951,lng: 16.5083635},{lat: 59.6333051,lng: 16.5084235},
				{lat: 59.6299051,lng: 16.5135235},{lat: 59.6295851,lng: 16.5142035},{lat: 59.6293951,lng: 16.5139935},
				{lat: 59.6293051,lng: 16.5139935},{lat: 59.6292051,lng: 16.5140535},{lat: 59.6291051,lng: 16.5141935},
				{lat: 59.6290551,lng: 16.5144435},{lat: 59.6280551,lng: 16.5156451},{lat: 59.6278551,lng: 16.5158351},
				{lat: 59.6276551,lng: 16.5160851},{lat: 59.6270551,lng: 16.5169851},{lat: 59.6268551,lng: 16.5172451},
				{lat: 59.6264551,lng: 16.5177451},{lat: 59.6263251,lng: 16.5179001},{lat: 59.6262851,lng: 16.5179101},
				{lat: 59.6262351,lng: 16.5178901},{lat: 59.6261551,lng: 16.5176501},{lat: 59.6261051,lng: 16.5176501},
				{lat: 59.6256051,lng: 16.5182801},{lat: 59.6254051,lng: 16.5185601},{lat: 59.6251651,lng: 16.5188991},
				{lat: 59.6244651,lng: 16.5197291},{lat: 59.6241651,lng: 16.5201491},{lat: 59.6236651,lng: 16.5209091},
				{lat: 59.6230651,lng: 16.5216491},{lat: 59.6225651,lng: 16.5222691},{lat: 59.6219951,lng: 16.5228591},
				{lat: 59.6218951,lng: 16.5228591},{lat: 59.6217951,lng: 16.5227591},{lat: 59.6217251,lng: 16.5226591},
				{lat: 59.6215051,lng: 16.5222591},{lat: 59.6213601,lng: 16.5220091},{lat: 59.6210601,lng: 16.5220991},
				{lat: 59.6208601,lng: 16.5221991},{lat: 59.6205601,lng: 16.5225991},{lat: 59.6202601,lng: 16.5230991},
				{lat: 59.6198901,lng: 16.5236991},{lat: 59.6195901,lng: 16.5239991},{lat: 59.6192901,lng: 16.5242991},
				{lat: 59.6190901,lng: 16.5244991},{lat: 59.6188901,lng: 16.5247991},{lat: 59.6185901,lng: 16.5255991},
				{lat: 59.6183901,lng: 16.5259991},{lat: 59.6178901,lng: 16.5265991},{lat: 59.6173901,lng: 16.5269991},
				{lat: 59.6170901,lng: 16.5273591},{lat: 59.6167201,lng: 16.5276991},{lat: 59.6166601,lng: 16.5276991},
				{lat: 59.6163601,lng: 16.5278791},{lat: 59.6160601,lng: 16.5285791},{lat: 59.6159601,lng: 16.5287491},
				{lat: 59.6157601,lng: 16.5290991},{lat: 59.6153601,lng: 16.5300991},{lat: 59.6152101,lng: 16.5304191},
				{lat: 59.6150101,lng: 16.5310591},{lat: 59.6148101,lng: 16.5316391},{lat: 59.6144101,lng: 16.5327591},
				{lat: 59.6141101,lng: 16.5336991},{lat: 59.6139101,lng: 16.5340991},{lat: 59.6138101,lng: 16.5342991},
				{lat: 59.6136101,lng: 16.5345991},{lat: 59.6135401,lng: 16.5349991},{lat: 59.6133401,lng: 16.5351891},
				{lat: 59.6131401,lng: 16.5354991},{lat: 59.6129401,lng: 16.5357171},{lat: 59.6127401,lng: 16.5360471},
				{lat: 59.6126001,lng: 16.5363201},{lat: 59.6127701,lng: 16.5367901},{lat: 59.6128701,lng: 16.5368951},
				{lat: 59.6130001,lng: 16.5368851},{lat: 59.6133001,lng: 16.5362851},{lat: 59.6137001,lng: 16.5356851},
				{lat: 59.6138001,lng: 16.5354851},{lat: 59.6139001,lng: 16.5352151},{lat: 59.6142001,lng: 16.5348151},
				{lat: 59.6147001,lng: 16.5337151},{lat: 59.6150001,lng: 16.5327151},{lat: 59.6151001,lng: 16.5325151},
				{lat: 59.6152001,lng: 16.5325151},{lat: 59.6153001,lng: 16.5324551},{lat: 59.6154001,lng: 16.5323551},
				{lat: 59.6155001,lng: 16.5320010},{lat: 59.6155001,lng: 16.5320010},{lat: 59.6156001,lng: 16.5315515},
				{lat: 59.6156501,lng: 16.5313215},{lat: 59.6157101,lng: 16.5311015},{lat: 59.6159101,lng: 16.5308015},
				{lat: 59.6160001,lng: 16.5302995},{lat: 59.6162001,lng: 16.5297095},{lat: 59.6164001,lng: 16.5291795},
				{lat: 59.6166001,lng: 16.5289595},{lat: 59.6166801,lng: 16.5289595},{lat: 59.6169001,lng: 16.5288295},
				{lat: 59.6170001,lng: 16.5288295},{lat: 59.6170501,lng: 16.5288295},{lat: 59.6171201,lng: 16.5286995},
				{lat: 59.6173201,lng: 16.5281495},{lat: 59.6174201,lng: 16.5279595},{lat: 59.6178201,lng: 16.5275295},
				{lat: 59.6180201,lng: 16.5273295},{lat: 59.6186201,lng: 16.5265895},{lat: 59.6189201,lng: 16.5262695},
				{lat: 59.6190601,lng: 16.5261595},{lat: 59.6191601,lng: 16.5260595},{lat: 59.6193601,lng: 16.5256595},
				{lat: 59.6195601,lng: 16.5252995},{lat: 59.6197601,lng: 16.5250295},{lat: 59.6202601,lng: 16.5244295},
				{lat: 59.6206001,lng: 16.5242000},{lat: 59.6208001,lng: 16.5239402},{lat: 59.6215001,lng: 16.5236402},
				{lat: 59.6217801,lng: 16.5234402},{lat: 59.6220001,lng: 16.5238202},{lat: 59.6222901,lng: 16.5239902},
				{lat: 59.6225301,lng: 16.5239902},{lat: 59.6227901,lng: 16.5242702},{lat: 59.6230901,lng: 16.5240502},
				{lat: 59.6232901,lng: 16.5237502},{lat: 59.6234301,lng: 16.5235620},{lat: 59.6235951,lng: 16.5231020},
				{lat: 59.6237551,lng: 16.5229320},{lat: 59.6239551,lng: 16.5226820},{lat: 59.6241951,lng: 16.5224820},
				{lat: 59.6243951,lng: 16.5222020},{lat: 59.6247951,lng: 16.5217520},{lat: 59.6249951,lng: 16.5215520},
				{lat: 59.6252051,lng: 16.5211520},{lat: 59.6252351,lng: 16.5209520},{lat: 59.6253351,lng: 16.5207020},
				{lat: 59.6255351,lng: 16.5205020},{lat: 59.6257351,lng: 16.5205020},{lat: 59.6259051,lng: 16.5203920},
				{lat: 59.6259951,lng: 16.5202020},{lat: 59.6261951,lng: 16.5198520},{lat: 59.6263951,lng: 16.5194520},
				{lat: 59.6265951,lng: 16.5191520},{lat: 59.6269951,lng: 16.5187520},{lat: 59.6273951,lng: 16.5184520},
				{lat: 59.6275951,lng: 16.5183520},{lat: 59.6278951,lng: 16.5181520},{lat: 59.6281951,lng: 16.5178520},
				{lat: 59.6283951,lng: 16.5176020},{lat: 59.6290951,lng: 16.5176920},{lat: 59.6292951,lng: 16.5176520},
				{lat: 59.6294951,lng: 16.5175920},{lat: 59.6299951,lng: 16.5177420},{lat: 59.6305651,lng: 16.5178720},
				{lat: 59.6307651,lng: 16.5179150},{lat: 59.6309651,lng: 16.5179150},{lat: 59.6311651,lng: 16.5179150},
				{lat: 59.6313651,lng: 16.5178550},{lat: 59.6315651,lng: 16.5176550},{lat: 59.6319651,lng: 16.5170550},
				{lat: 59.6320651,lng: 16.5169550},{lat: 59.6323651,lng: 16.5169550},{lat: 59.6326651,lng: 16.5169050},
				{lat: 59.6328651,lng: 16.5169050},{lat: 59.6330651,lng: 16.5169450},{lat: 59.6332651,lng: 16.5170450},
				{lat: 59.6342651,lng: 16.5174450},{lat: 59.6345051,lng: 16.5176750},{lat: 59.6347051,lng: 16.5176750},
				{lat: 59.6348051,lng: 16.5177150},{lat: 59.6349951,lng: 16.5179050},{lat: 59.6352951,lng: 16.5182050},
				{lat: 59.6354951,lng: 16.5184950},{lat: 59.6356951,lng: 16.5186950},{lat: 59.6358951,lng: 16.5188050},
				{lat: 59.6362951,lng: 16.5194050},{lat: 59.6364951,lng: 16.5198950},{lat: 59.6366951,lng: 16.5202050},
				{lat: 59.6368951,lng: 16.5204250},{lat: 59.6369951,lng: 16.5204950},{lat: 59.6370951,lng: 16.5204950},
				{lat: 59.6371951,lng: 16.5204950},{lat: 59.6372951,lng: 16.5205050},{lat: 59.6376851,lng: 16.5209550},
				{lat: 59.6377851,lng: 16.5209950},{lat: 59.6383051,lng: 16.5209950},{lat: 59.6383451,lng: 16.5210950},
				{lat: 59.6383651,lng: 16.5214950},{lat: 59.6384251,lng: 16.5218050},{lat: 59.6385951,lng: 16.5222550},
				{lat: 59.6387951,lng: 16.5227550},{lat: 59.6389951,lng: 16.5230550},
			];

			function initMap() {
				map = new google.maps.Map(document.getElementById('map'), {
					zoom: 13,
					center: {lat: 59.6173901, lng: 16.5269991},
					mapTypeId: 'satellite'
				});

				RacePath = new google.maps.Polyline({
					path: RaceCoordinates,
					geodesic: true,
					strokeColor: 'Darkred',
					strokeOpacity: 0.5,
					strokeWeight: 12
				});

				RacePath.setMap(map);
				marker = new google.maps.Marker({
					position: {lat: 59.6390965, lng: 16.5230476},
					map: map,
					title: "Station No.1"
				});

				marker = new google.maps.Marker({
					position: {lat: 59.6217801, lng: 16.5234402},
					map: map,
					title: "Station No.2"
				});
				marker = new google.maps.Marker({
					position: {lat: 59.6560947, lng: 16.4921747},
					map: map,
					title: "Station No.3"
				});
				marker = new google.maps.Marker({
					position: {lat: 59.6389951, lng: 16.5230550},
					map: map,
					title: "Station No.4"
				});

				cityCircle = new google.maps.Circle({});

				drawCircle(currentPos);
			}

			function drawCircle(index1) {
				cityCircle = new google.maps.Circle({
					strokeColor: 'red',
					strokeOpacity: 0.8,
					strokeWeight: 6,
					fillColor: 'darkgreen',
					fillOpacity: 0.8,
					map: map,
					center: {
						lat:RaceCoordinates[index1].lat, 
						lng:RaceCoordinates[index1].lng
					},
					radius: 110
				});
				
				Ref();
			}

			function Ref() {
				$.ajax({
					type: 'post',
					url: 'pages/updatePos.php',
					data: {
						currentPos: currentPos,
						runner: <?=$runner_id?>,
						race: <?=$race_id?>
					},
					success: function (response) {
						if(response < 475) {
							currentPos = response;
						}
						
						else {
							currentPos = 475;
						}

						cityCircle.setMap(null);

						cityCircle = new google.maps.Circle({
							strokeColor: 'red',
							strokeOpacity: 0.8,
							strokeWeight: 6,
							fillColor: 'red',
							fillOpacity: 0.8,
							map: map,
							center: {
								lat: RaceCoordinates[parseFloat(currentPos)].lat,
								lng: RaceCoordinates[parseFloat(currentPos)].lng
							},
							radius: 110
						});
					}
				});

				timer1();
			}
			
			function timer1() {
				setTimeout(
					function () {
						Ref();
					},
					2000
				);
			}
		</script>

		<script async defer
				src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCX8z4OcH1aedxkQVzmd-SFi98wFUhljco&callback=initMap">
		</script>
	</div>
</main>