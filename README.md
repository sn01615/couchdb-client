# couchdb-client
CouchDB Client library

```php
# 连接
$client = new CouchDBClient([
    'server' => 'http://127.0.0.1:5984'
]);

# 测试
$version = $client->get();

# 获取所有数据库
$dbs = $client->getAllDbs();

# 创建数据库
$result = $client->put($dbName);

# 获取数据库信息
$result = $client->get($dbName);

# 插入一个文档
$document = new stdClass();
$document->id = 123;
$document->text = "hello 中文";

$result = $client->setDbName($dbName)
    ->putDocument($testKey, $document);

# 更新一个文档
$document = new stdClass();
$document->id = 123;
$document->text = "hello 中文";
$document->_rev = $result->_rev;

$result = $client->setDbName($dbName)
    ->updateDocument($testKey, $document);

# 替换或插入一个文档
$document = new stdClass();
$document->id = 123;
$document->text = "hello 中文";

$result = $instance->setDbName($dbName)
    ->upsertDocument($testKey, $document);

# get uuid
$result = $instance->setDbName($dbName)->getUiid();

# 删除一个数据库
$result = $instance->delete($dbName);
```
