<?php
	include 'include/config.php';
	include 'include/db.php';
	include 'include/user.class.php';
	
	echo "<h1>Dynamic DB class testing</h1>";
	$devInfoData = new DeviceInfoData();
	echo "nr of fields: " . $devInfoData->sdmObjSize . "<br>";
	echo "id: ". $devInfoData->id . "<br>";
	$devInfoData->device_info_id = 1;
	$devInfoData->key = "sampleKey";
	$devInfoData->value = "sampleValue";
	$id = $devInfoData->create();
	$id = $id == null ? "null":$id;
	echo "created id: " . $id . "<br>";
	echo "new id: ". $devInfoData->id . "<br>";
	$devInfoData->longvalue = "longValue";
	$status = $devInfoData->update();
	echo "update status: " . $status . "<br>";
	$devInfoData->update();
	echo "update status: " . $status . "<br>";
	
	echo "<h1>Testing device_info_data</h1>";
	$devInfo = new DeviceInfoData(1);
	echo "nr of fields (id=1): " . $devInfo->sdmObjSize . "<br>";
	echo "key: ". $devInfo->key . "<br>";
	
	echo "<h1>Testing device_info</h1>";
	$devInfo = new DeviceInfo(1);
	echo "nr of fields: " . $devInfo->sdmObjSize . "<br>";
	echo "id: ". $devInfo->id . "<br>";
	$devInfo->type = "newType";
	$id = $devInfo->update();
	$status = $devInfo->update();
	echo "update status: " . $status . "<br>";
	$devInfo->loadData();
	echo "load data status: " . $status . "<br>";
	echo $devInfo->data[0]->value. "<br>";
	echo $devInfo->getData("id",1)->value. "<br>";
	echo $devInfo->getData("key","needle")->value. "<br>";
	
	echo "<h1>Testing device</h1>";
	$dev= new Device(1);
	echo "nr of fields: " . $dev->sdmObjSize . "<br>";
	echo "name: ". $dev->name . "<br>";
	$dev->name = "newName";
	$status = $dev->update();
	echo "update status: " . $status . "<br>";
	echo $dev->getLocation("id",3)->longitude. "<br>";
	echo $dev->getInfo("type","newType")->getData("key","needle")->value. "<br>";
	$data =$dev->getInfo("type","newType")->getData("key","needle");
	$data->longvalue="testn";
	$data->update();
	
	echo "<h1>Testing user</h1>";
	$usr= new User(1);
	echo "nr of fields: " . $usr->sdmObjSize . "<br>";
	echo "name: ". $usr->username . "<br>";
	$usr->username = "newUsrName";
	$status = $usr->update();
	echo "update status: " . $status . "<br>";
	echo $usr->getSetting("key", "lang")->value. "<br>";
	echo $usr->getDevice("id",1)->name. "<br>";
?>