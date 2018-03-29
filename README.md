# Is It Wet

An API for determine if it's on the water according to a point's coordinates.

Send coordinates parameter to index.php as GET method.

### Example

Request
```
/index.php?coordinates=40.362804,27.980187
```

Response
```
{
	"latitude": 40.362804,
	"longitude": 27.980187,
	"is_wet": true
}
```
