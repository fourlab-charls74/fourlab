<script type="text/javascript">

    let data = {
        name: 'Root',
        children: []
    };

    $(document).ready(function() {
        Search();
    });

    function tooltipRenderer(params) {
        const { datum } = params;
        let content = '<div>';
        const customRootText = 'Root';
        const title = datum.parent
            ? datum.parent.depth
                ? datum.parent.label
                : customRootText
            : customRootText;

        content += `<div style="font-weight: normal; color: black; padding: 5px;">`;

        if(datum.data.description){
            content += `(${datum.data.name}) ${datum.data.description}: ${String(
                isFinite(datum.colorValue) ? datum.colorValue.toFixed(2) : '')}%`;
        } else {
            content += `${datum.label}`;
        }
        content += '</div>';
        return {
            title,
            content,
            backgroundColor: 'gray',
        };
    }

    function Search(){

        document.getElementById('myChart').innerHTML = '';

        $.ajax({
            type: "get",
            url: '/head/dashboard/search',
            contentType: "application/x-www-form-urlencoded; charset=utf-8",
            dataType: 'json',
            data: $('form[name="search"]').serialize(),
            success: function(res) {
                data.children = res.data;
                var options = {
                    type: 'hierarchy',
                    container: document.getElementById('myChart'),
                    data,
                    series: [
                        {
                            type: 'treemap',
                            labelKey: 'name', // defaults to 'label', but current dataset uses 'name'
                            sizeKey: 'size', // default (can be omitted for current dataset)
                            colorKey: 'color', // default (can be omitted for current dataset)
                            labels: {
                                large: {
                                    color: 'white', // default: 'white'
                                    fontWeight: 'normal', // default: 'bold'
                                    fontSize: 10, // default: 18
                                },
                                medium: {
                                    color: 'white', // default: 'white'
                                    fontWeight: 'normal', // default: 'bold'
                                    fontSize: 10, // default: 18
                                },
                                small: {
                                    color: 'white', // default: 'white'
                                    fontWeight: 'normal', // default: 'bold'
                                    fontSize: 10, // default: 18
                                },
                                color: {
                                    color: 'white', // default: 'white'
                                    fontWeight: 'normal', // default: 'bold'
                                    fontSize: 9, // default: 18
                                }
                            },
                            tooltip: {
                                renderer: tooltipRenderer,
                            },
                            nodePadding:7,
                            //gradient:false,
                            colorDomain:[0,100],
                            //colorRange:['white','orange','yellow','green','blue','indigo','red'],
                            colorRange:['green','red'],
                            listeners: {
                                nodeClick: function (event) {
                                    var datum = event.datum;
                                    window.alert('click');
                                },
                            }
                        },
                    ],
                };
                agCharts.AgChart.create(options);
            },
            error: function(e) {
                console.log(e.responseText)
            }
        });
    }

</script>
