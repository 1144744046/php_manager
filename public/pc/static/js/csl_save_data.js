
var Csl_Local_Data={
	save_city:function(code,name){
		var jsonData = {'code': code, 'name': name}; // 定义一个JSON对象
		var str_jsonData = JSON.stringify(jsonData);
		localStorage.setItem('local_data_city', str_jsonData);
	},
	get_city:function(){
		var getLocalData = localStorage.getItem('local_data_city'); // 读取字符串数据
		var jsonObj = JSON.parse(getLocalData);
		return jsonObj;
	}
}
