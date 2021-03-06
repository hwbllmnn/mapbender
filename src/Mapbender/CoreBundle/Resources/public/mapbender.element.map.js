(function($) {

OpenLayers.ProxyHost = Mapbender.configuration.application.urls.proxy + '?url=';

$.widget("mapbender.mbMap", {
    options: {
        layerset: null, //mapset for main map
        dpi: OpenLayers.DOTS_PER_INCH,
        srs: 'EPSG:4326',
        units: 'degrees',
        extents: {
            max: [-180, -90, 180, 90],
            start: [-180, -90, 180, 90]
        },
        maxResolution: 'auto',
        imgPath: 'bundles/mapbendercore/mapquery/lib/openlayers/img'
    },

    map: null,
    highlightLayer: null,

    _create: function() {
        var self = this,
            me = $(this.element);

        if(typeof(this.options.dpi) !== 'undefined') {
            OpenLayers.DOTS_PER_INCH = this.options.dpi;
        }

        OpenLayers.ImgPath = Mapbender.configuration.assetPath + this.options.imgPath + '/';

        // Prepare initial layers
        var layers = [];
        this.rootLayers = [];
        var allOverlays = true;

        // TODO think about integrating proj4js properly into Mapbender and/or Mapquery
        // could also integrate transformation on server side
        if(this.options.targetsrs && window.Proj4js) {
            var bbox = this.options.extents.max;
            var source = new Proj4js.Proj(this.options.srs);
            var dest = new Proj4js.Proj(this.options.targetsrs);
            var min = Proj4js.transform(source, dest, {x: bbox[0], y: bbox[1]});
            var max = Proj4js.transform(source, dest, {x: bbox[2], y: bbox[3]});
            var newExtent = [min.x, min.y, max.x, max.y];
            this.options.extents.max = newExtent;
            this.options.srs = this.options.targetsrs;

            bbox = this.options.extents.start;
            min = Proj4js.transform(source, dest, {x: bbox[0], y: bbox[1]});
            max = Proj4js.transform(source, dest, {x: bbox[2], y: bbox[3]});
            this.options.extents.start = [min.x, min.y, max.x, max.y];
            if(this.options.extra && this.options.extra.type === 'bbox') {
                min = Proj4js.transform(source, dest, {x: this.options.extra.data.xmin, y: this.options.extra.data.ymin});
                max = Proj4js.transform(source, dest, {x: this.options.extra.data.xmax, y: this.options.extra.data.ymax});
                this.options.extra.data.xmin = min.x;
                this.options.extra.data.ymin = min.y;
                this.options.extra.data.xmax = max.x;
                this.options.extra.data.ymax = max.y;
            }
            if(this.options.targetsrs === 'EPSG:4326') {
                this.options.units = 'degrees';
            }
        }

        function addSubs(layer){
            if(layer.sublayers) {
                $.each(layer.sublayers, function(idx, val) {
                           layers.push(val);
                           addSubs(val);
                       });
            }
        }

        $.each(Mapbender.configuration.layersets[this.options.layerset], function(idx, layerDef) {
                   layers.push(self._convertLayerDef.call(self, layerDef));
                   self.rootLayers.push(layers[layers.length-1]);
                   addSubs(layers[layers.length-1]);
                   allOverlays = allOverlays && (layerDef.configuration.baselayer !== true);
        });

        // TODO find out how to do a proper menu with jquery ui
        if(this.options.controlstype === 'menu') {
            $('#mb-element-maptools').buttonset();
        } else {
            $('#mb-element-maptools').buttonset();
        }

        var controls = [];
        var hasNavigation = false;
        if(this.options.controls) {
            $.each(this.options.controls, function(idx, control) {
                       switch(idx) {
                       case 'pan':
                           $('#' + control.target).css('display', 'block')
                               .click(function() { self.map.mode('pan'); });
                           control = 'DragPan';
                           break;
                       case 'zoomin':
                           $('#' + control.target).css('display', 'block')
                               .click(function() { self.map.mode('zoomin'); });
                           control = 'ZoomIn';
                           break;
                       case 'zoomout':
                           $('#' + control.target).css('display', 'block')
                               .click(function() { self.map.mode('zoomout'); });
                           control = 'ZoomOut';
                           break;
                       case 'zoombox':
                           $('#' + control.target).css('display', 'block')
                               .click(function() { self.map.mode('zoombox'); });
                           control = 'ZoomBox';
                           break;
                       case 'zoomhome':
                       case 'zoomnext':
                       case 'zoomlast':
                       case 'zoomcoordinate':
                       case 'zoomscale':
                           $('#' + control.target).css('display', 'block');
                           break;
                       }
                       if(idx === 'zoomhome') {
                           $('#' + control.target).click($.proxy(self.zoomToFullExtent, self));
                       } else if(idx === 'zoomlast') {
                           if(!hasNavigation) {
                               controls.push(hasNavigation = new OpenLayers.Control.NavigationHistory());
                           }
                           $('#' + control.target).click($.proxy(hasNavigation.previousTrigger, hasNavigation));
                       } else if(idx === 'zoomnext') {
                           if(!hasNavigation) {
                               controls.push(hasNavigation = new OpenLayers.Control.NavigationHistory());
                           }
                           $('#' + control.target).click($.proxy(hasNavigation.nextTrigger, hasNavigation));
                       } else if(idx === 'zoomcoordinate') {
                           $('#mb-zoom-coordinate-dialog').dialog(
                               {
                                   autoOpen: false,
                                   buttons: {
                                       Zoom: function() {
                                           var x = $('#mb-zoom-coordinate-x')[0].value;
                                           var y = $('#mb-zoom-coordinate-y')[0].value;
                                           self.center({position: [x, y]});
                                           $( this ).dialog( "close" );
                                       },
                                       Cancel: function() {
                                           $( this ).dialog( "close" );
                                       }
                                   }});
                           $('#' + control.target).click(function(){ $('#mb-zoom-coordinate-dialog').dialog('open'); });
                       } else if(idx === 'zoomscale') {

                           $('#mb-zoom-scale-dialog').dialog(
                               {
                                   autoOpen: false,
                                   buttons: {
                                       Zoom: function() {
                                           var scale = $('#mb-zoom-scale-select')[0].value;
                                           self.map.olMap.zoomToScale(scale);
                                           $( this ).dialog( "close" );
                                       },
                                       Cancel: function() {
                                           $( this ).dialog( "close" );
                                       }
                                   }});
                           $('#' + control.target).click(function(){
                                                             var scales = self.map.olMap.scales;
                                                             var html = '';
                                                             $.each(scales, function(idx, val) {
                                                                        html += '<option>' + val + '</option>';
                                                                    });
                                                             $('#mb-zoom-scale-select').html(html);
                                                             $('#mb-zoom-scale-dialog').dialog('open');
                                                         });
                       } else {
                           var ctrl = new OpenLayers.Control[control]();
                           controls.push(ctrl);
                       }
                   });
        }

        var mapOptions = {
            maxExtent: this.options.extents.max,
            zoomToMaxExtent: false,
            maxResolution: this.options.maxResolution,
            numZoomLevels: this.options.numZoomLevels,
            projection: new OpenLayers.Projection(this.options.srs),
            displayProjection: new OpenLayers.Projection(this.options.srs),
            units: this.options.units,
            allOverlays: allOverlays,
            theme: null
            //layers: layers
        };


        if(controls.length !== 0) mapOptions.controls = controls;

        if(this.options.scales) {
            $.extend(mapOptions, {
                scales: this.options.scales
            });
        }

        me.mapQuery(mapOptions);
        this.map = me.data('mapQuery');
        // if(hasNavigation){
        //     hasNavigation.setMap(this.map.olMap);
        //     hasNavigation.activate();
        // }

        //TODO: Bind all events
        this.map.bind('zoomend', function() { self._trigger('zoomend', arguments); });

        // We have to add our listeners to the map before adding layers...
        // This might change in the future, when the MapQuery map accepts
        // listeners as options
        this.map.bind('mqAddLayer', $.proxy(this._onAddLayer, this));
        this.map.bind('mqRemoveLayer', $.proxy(this._onRemoveLayer, this));

        this.map.layers(layers);
        this.map.center({ box: this.options.extents.max });

        if(this.options.extents.start) {
            this.map.center({
                box: this.options.extents.start
            });
        }
        if(this.options.extra.type) {
            switch(this.options.extra.type) {
                case 'poi':
                    this.map.center({
                        position: [ this.options.extra.data.x,
                            this.options.extra.data.y ]
                    });
                    if(this.options.extra.data.scale) {
                        this.zoomToScale(this.options.extra.data.scale);
                    }

                    if(this.options.extra.data.label) {
                        var position = new OpenLayers.LonLat(
                            this.options.extra.data.x,
                            this.options.extra.data.y);
                        var popup = new OpenLayers.Popup.FramedCloud('chicken',
                            position,
                            null,
                            this.options.extra.data.label,
                            null,
                            true,
                            function() {
                                self.removePopup(this);
                                this.destroy();
                            });
                        this.addPopup(popup);
                    }
                    break;
                case 'bbox':
                    this.map.center({
                        box: [
                            this.options.extra.data.xmin, this.options.extra.data.ymin,
                            this.options.extra.data.xmax, this.options.extra.data.ymax
                        ]});
            }
        }

        if(this.options.overview) {

            var layers_ = [];
            $.each(Mapbender.configuration
                    .layersets[this.options.overview.layerset],
                    function(idx, layerDef) {
                layers_.push(self._convertLayerDef.call(self, layerDef));
            });

            var res = $.MapQuery.Layer.types[layers_[0].type]
                .call(this, layers_[0]);

            var div = $("#"+this.options.overview.div);
            div = div.size() > 0 ? div.get(0): undefined;
            var overviewOptions = {
                layers: [res.layer],
                div: div,
                mapOptions: {
                    maxExtent: OpenLayers.Bounds.fromArray(this.options.extents.max),
                    projection: new OpenLayers.Projection(this.options.srs),
                    theme: null
                }
            };

            if(this.options.overview.fixed) {
                $.extend(overviewOptions, {
                    minRatio: 1,
                    maxRatio: 1000000000
                });
            }
            var overviewControl = new OpenLayers.Control.OverviewMap(overviewOptions);
            this.map.olMap.addControl(overviewControl);
        }
        if(controls.length === 0) {
            this.map.olMap.addControl(new OpenLayers.Control.Scale());
        }

        self._trigger('ready');
    },

    /**
     * DEPRECATED
     */
    "goto": function(options) {
        this.map.center(options);
    },

    center: function(options) {
        this.map.center(options);
    },

    highlight: function(features, options) {
        var self = this;
        if(!this.highlightLayer) {
            this.highlightLayer = this.map.layers({
                type: 'vector',
                label: 'Highlight'});
            var selectControl = new OpenLayers.Control.SelectFeature(this.highlightLayer.olLayer, {
                hover: true,
                onSelect: function(feature) {
                    self._trigger('highlighthoverin', null, { feature: feature });
                },
                onUnselect: function(feature) {
                    self._trigger('highlighthoverout', null, { feature: feature });
                }
            });
            this.map.olMap.addControl(selectControl);
            selectControl.activate();
        }


        var o = $.extend({}, {
            clearFirst: true,
            "goto": true
        }, options);

        // Remove existing features if requested
        if(o.clearFirst) {
            this.highlightLayer.olLayer.removeAllFeatures();
        }

        // Add new highlight features
        this.highlightLayer.olLayer.addFeatures(features);

        // Goto features if requested
        if(o['goto']) {
            var bounds = this.highlightLayer.olLayer.getDataExtent();
            this.map.center({box: bounds.toArray()});
        }

        this.highlightLayer.bind('featureselected',   function() { self._trigger('highlightselected', arguments); });
        this.highlightLayer.bind('featureunselected', function() { self._trigger('highlightunselected', arguments); });
    },

    layer: function(layerDef) {
        var l = this._convertLayerDef(layerDef)
        this.rootLayers.push(l);
        this.map.layers(l);
    },

    // untested!
    appendLayer: function(layerDef, parentId) {
        if(!parentId) {
            this.layer(layerDef);
            return;
        }
        var self = this;
        var newLayer = this._convertLayerDef(layerDef);
        this.map.layers(newLayer);

        function _append(_, layer) {
            if(layer.mapbenderId === parentId) {
                if(!layer.sublayers) layer.sublayers = [];
                layer.sublayers.push(newLayer);
            } else {
                if(layer.sublayers) {
                    $.each(layer.sublayers, _append);
                }
            }
        }

        $.each(this.rootLayers, _append);

        this.rebuildStacking();
    },

    /**
     * Insert a layer before or after a sibling. Untested!
     */
    insert: function(layerDef, siblingId, before) {
        var self = this;
        var newLayer = this._convertLayerDef(layerDef);
        this.map.layers(newLayer);

        function _insert(list) {
            for(var i = 0; i < list.length; ++i) {
                if(list[i].mapbenderId === siblingId) {
                    if(before) {
                        list.splice(i, 0, newLayer);
                        break;
                    } else {
                        list.splice(i+1, 0, newLayer);
                        break;
                    }
                } else {
                    if(list[i].sublayers) {
                        _insert(list[i].sublayers);
                    }
                }
            }
        }

        _insert(this.rootLayers);

        this.rebuildStacking();
    },

    rebuildStacking: function() {
        var self = this;
        var pos = 0;
        function _rebuild(layer){
            if(layer.sublayers) {
                $.each(layer.sublayers, function(idx, val) {
                           self.layerById(val.mapbenderId).position(pos++);
                           _rebuild(val);
                       });
            }
        }

        for(var i = 0; i < this.rootLayers.length; ++i) {
            self.layerById(this.rootLayers[i].mapbenderId).position(pos++);
            _rebuild(this.rootLayers[i]);
        }
    },

    /**
     * Moves a layer up (direction == true) or down (direction == false) on the same level in the layer hierarchy.
     */
    move: function(id, direction) {
        var self = this;
        function _move(list) {
            var idx = null;
            for(var i = 0; i < list.length; ++i) {
                if(list[i].mapbenderId === id) {
                    idx = i;
                    break;
                }
            }
            if(idx !== null) {
                if(direction && idx > 0) {
                    var lay = list[idx];
                    list[idx] = list[idx-1];
                    list[idx-1] = lay;
                    lay = self.layerById(id);
                    self.rebuildStacking();
                } else if(!direction && idx < list.length - 1) {
                    var lay = list[idx];
                    list[idx] = list[idx+1];
                    list[idx+1] = lay;
                    lay = self.layerById(id);
                    self.rebuildStacking();
                }
            } else {
                for(i = 0; i < list.length; ++i) {
                    if(list[i].sublayers) {
                        _move(list[i].sublayers);
                    }
                }
            }
        }
        _move(this.rootLayers);
    },

    _convertLayerDef: function(layerDef) {
        var self = this;
        if(typeof Mapbender.layer[layerDef.type] !== 'object'
            && typeof Mapbender.layer[layerDef.type].create !== 'function') {
            throw "Layer type " + layerDef.type + " is not supported by mapbender.mapquery-map";
        }
        // TODO object should be cleaned up
        var l = $.extend(Mapbender.layer[layerDef.type].create(layerDef), { mapbenderId: layerDef.id, configuration: layerDef });

        if(layerDef.configuration.sublayers) {
            l.sublayers = [];
            $.each(layerDef.configuration.sublayers, function(idx, val) {
                       l.sublayers.push(self._convertLayerDef({id: idx, type: 'wms', configuration: val}));
                   });
        }
        return l;
    },

    zoomIn: function() {
        // TODO: MapQuery?
        this.map.olMap.zoomIn();
    },

    zoomOut: function() {
        // TODO: MapQuery?
        this.map.olMap.zoomOut();
    },

    zoomToFullExtent: function() {
        // TODO: MapQuery?
        this.map.olMap.zoomToMaxExtent();
    },

    zoomToExtent: function(extent, scale) {
        //TODO: MapQuery?
        this.map.olMap.zoomToExtent(extent);
        if(scale) {
            this.map.olMap.zoomToScale(scale, true);
        }
    },

    zoomToScale: function(scale) {
        this.map.olMap.zoomToScale(scale, true);
    },

    panMode: function() {
        this.map.mode('pan');
    },

    addPopup: function(popup) {
        //TODO: MapQuery
        this.map.olMap.addPopup(popup);
    },

    removePopup: function(popup) {
        //TODO: MapQuery
        this.map.olMap.removePopup(popup);
    },

    removeById: function(id) {
        var self = this;
        function _remove(_, layer) {
            self.layerById(layer.mapbenderId).remove();
            if(layer.sublayers) {
                $.each(layer.sublayers, _remove);
            }
        }

        $.each(this.rootLayers, function(idx, layer) {
                   if(layer.mapbenderId === id) {
                       _remove(null, layer);
                   }
        });
    },

    /**
     * Searches for a MapQuery layer by it's Mapbender id.
     * Returns the layer or null if not found.
     */
    layerById: function(id) {
        var layer = null;
        $.each(this.map.layers(), function(idx, mqLayer) {
            if(mqLayer.options.mapbenderId === id) {
                layer = mqLayer;
                return false;
            }
        });
        return layer;
    },

    scales: function() {
        var scales = [];
        for(var i = 0; i < this.map.olMap.getNumZoomLevels(); ++i) {
            var res = this.map.olMap.getResolutionForZoom(i);
            scales.push(OpenLayers.Util.getScaleFromResolution(res, this.map.olMap.units));
        }
        return scales;
    },

    /**
     * Listen to newly added layers in the MapQuery object
     */
    _onAddLayer: function(event, layer) {
        var listener = Mapbender.layer[layer.olLayer.type].onLoadStart;
        if(typeof listener === 'function') {
            listener.call(layer);
        }
    },


    /**
     * Listen to removed layer in the MapQuery object
     */
    _onRemoveLayer: function(event, layer) {}
});

})(jQuery);
