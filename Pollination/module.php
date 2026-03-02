<?php

declare(strict_types=1);

// Generell funktions
require_once __DIR__ . '/../libs/_traits.php';

/**
 * CLASS PollenCount
 */
class PollenCount extends IPSModuleStrict
{
    // Helper Traits
    use DebugHelper;
    use EventHelper;
    use ProfileHelper;
    use VariableHelper;
    use VersionHelper;

    /**
     * @var int Min IPS Object ID
     */
    private const IPS_MIN_ID = 10000;

    /**
     * @var string JSON Data URL
     */
    private const JSON = 'https://opendata.dwd.de/climate_environment/health/alerts/s31fg.json';

    /**
     * @var array<int,mixed> JPGs (Definition of the media to be created for the pollen calendars, with property name, filename and URL)
     */
    private const JPGS = [
        ['MediaAll',    'All-German pollen calendar (2016–2021)',                      'pollengermany', 'https://www.pollenstiftung.de/fileadmin/_processed_/3/7/csm_PID_Pollenflugkalender_5_0__2016-2021_web_30ddb87d31.jpg'],
        ['MediaNorth',  'Pollen calendar for northern Germany (2016–2021)',            'pollennorth',   'https://www.pollenstiftung.de/fileadmin/_processed_/9/6/csm_Nord_2023_kalendar_2016_2021_fuer_web_75e942a467.jpg'],
        ['MediaEast',   'Pollen calendar for central and eastern Germany (2016–2021)', 'polleneast',    'https://www.pollenstiftung.de/fileadmin/_processed_/6/b/csm_Mitte_2023_kalendar_2016_2021_fuer_web_7cbf4b26cb.jpg'],
        ['MediaSouth',  'Pollen calendar for southern Germany (2016–2021)',            'pollensouth',   'https://www.pollenstiftung.de/fileadmin/_processed_/2/4/csm_Sued_2023_kalendar_2016_2021_fuer_web_4ca05f0198.jpg'],
        ['MediaWest',   'Pollen calendar for western Germany (2016–2021)',             'pollenwest',    'https://www.pollenstiftung.de/fileadmin/_processed_/e/a/csm_West_2023_kalendar_2016_2021_fuer_web_2014e79f08.jpg'],
    ];

    /**
     * @var string Pollenflugkalender URL
     */
    //private const JPEG = 'https://www.wetterdienst.de/imgs/pollenflugkalendar.jpg';

    /**
     * @var array<string,string> Pollen (Mapping of property to pollen name)
     */
    private const POLLEN = [
        'ShowRagweed' => 'Ambrosia',
        'ShowMugwort' => 'Beifuss',
        'ShowBirch'   => 'Birke',
        'ShowAlder'   => 'Erle',
        'ShowAsh'     => 'Esche',
        'ShowGrasses' => 'Graeser',
        'ShowHazel'   => 'Hasel',
        'ShowRye'     => 'Roggen',
    ];

    /**
     * @var array<string|int,int> Level (Scale to Level)
     */
    private const LEVEL = [
        '0' => 0, '0-1' => 1, '1' => 2, '1-2' => 3, '2' => 4, '2-3' => 5, '3' => 6,
    ];

    /**
     * @var array<string,mixed> Definition of the date presentation for the visualization
     */
    private const POLLEN_PRESENTATION_DT = [
        'PRESENTATION'    => VARIABLE_PRESENTATION_DATE_TIME,
        'TIME'            => 1,
        'MONTH_TEXT'      => true,
        'DAY_OF_THE_WEEK' => true,
        'DATE'            => 2,
    ];

    /**
     * @var array<string,mixed> States (Bundesländer) presentation for the visualization
     */
    private const POLLEN_PRESENTATION_STATE = [
        'PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION,
        'OPTIONS'      => '[{"Caption":"Schleswig—Holstein und Hamburg","Color":-1,"IconActive":false,"IconValue":"","Value":10},{"Caption":"Mecklenburg—Vorpommern ","Color":-1,"IconActive":false,"IconValue":"","Value":20},{"Caption":"Niedersachsen und Bremen","Color":-1,"IconActive":false,"IconValue":"","Value":30},{"Caption":"Nordrhein—Westfalen","Color":-1,"IconActive":false,"IconValue":"","Value":40},{"Caption":"Brandenburg und Berlin ","Color":-1,"IconActive":false,"IconValue":"","Value":50},{"Caption":"Sachsen—Anhalt","Color":-1,"IconActive":false,"IconValue":"","Value":60},{"Caption":"Thüringen","Color":-1,"IconActive":false,"IconValue":"","Value":70},{"Caption":"Sachsen","Color":-1,"IconActive":false,"IconValue":"","Value":80},{"Caption":"Hessen","Color":-1,"IconActive":false,"IconValue":"","Value":90},{"Caption":"Rheinland—Pfalz und Saarland","Color":-1,"IconActive":false,"IconValue":"","Value":100},{"Caption":"Baden—Württemberg","Color":-1,"IconActive":false,"IconValue":"","Value":110},{"Caption":"Bayern","Color":-1,"IconActive":false,"IconValue":"","Value":120}]',
        'LAYOUT'       => 0,
        'ICON'         => 'flag',
        'DISPLAY'      => 0,
    ];

    /**
     * @var array<int,mixed> Regions (Teilgebiete) presentation for the visualization
     * The presentation is defined for each state, as the regions differ between states. The value of the state variable is used to select the correct presentation for the region variable.
     */
    private const POLLEN_PRESENTATION_REGIONS = [
        10  => ['init' => 11,  'template' => ['PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION, 'OPTIONS' => '[{"Caption":"Inseln und Marschen","Color":-1,"IconActive":false,"IconValue":"","Value":11},{"Caption":"Geest, Schleswig-Holstein und Hamburg","Color":-1,"IconActive":false,"IconValue":"","Value":12}]', 'LAYOUT' => 0, 'ICON' => 'location-dot', 'DISPLAY' => 0]],
        20  => ['init' => -1,  'template' => ['PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION, 'OPTIONS' => '[{"Caption":"Mecklenburg-Vorpommern ","Color":-1,"IconActive":false,"IconValue":"","Value":-1}]', 'LAYOUT' => 0, 'ICON' => 'Image', 'DISPLAY' => 0]],
        30  => ['init' => 31,  'template' => ['PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION, 'OPTIONS' => '[{"Caption":"Westl. Niedersachsen/Bremen","Color":-1,"IconActive":false,"IconValue":"","Value":31},{"Caption":"Östl. Niedersachsen","Color":-1,"IconActive":false,"IconValue":"","Value":32}]', 'LAYOUT' => 0, 'ICON' => 'location-dot', 'DISPLAY' => 0]],
        40  => ['init' => 41,  'template' => ['PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION, 'OPTIONS' => '[{"Caption":"Rhein.-Westfäl. Tiefland","Color":-1,"IconActive":false,"IconValue":"","Value":41},{"Caption":"Ostwestfalen","Color":-1,"IconActive":false,"IconValue":"","Value":42},{"Caption":"Mittelgebirge NRW","Color":-1,"IconActive":false,"IconValue":"","Value":43}]', 'LAYOUT' => 0, 'ICON' => 'Image', 'DISPLAY' => 0]],
        50  => ['init' => -1,  'template' => ['PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION, 'OPTIONS' => '[{"Caption":"Brandenburg und Berlin ","Color":-1,"IconActive":false,"IconValue":"","Value":-1}]', 'LAYOUT' => 0, 'ICON' => 'location-dot', 'DISPLAY' => 0]],
        60  => ['init' => 61,  'template' => ['PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION, 'OPTIONS' => '[{"Caption":"Tiefland Sachsen-Anhalt","Color":-1,"IconActive":false,"IconValue":"","Value":61},{"Caption":"Harz","Color":-1,"IconActive":false,"IconValue":"","Value":62}]', 'LAYOUT' => 0, 'ICON' => 'location-dot', 'DISPLAY' => 0]],
        70  => ['init' => 71,  'template' => ['PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION, 'OPTIONS' => '[{"Caption":"Tiefland Thüringen","Color":-1,"IconActive":false,"IconValue":"","Value":71},{"Caption":"Mittelgebirge Thüringen","Color":-1,"IconActive":false,"IconValue":"","Value":72}]', 'LAYOUT' => 0, 'ICON' => 'location-dot', 'DISPLAY' => 0]],
        80  => ['init' => 81,  'template' => ['PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION, 'OPTIONS' => '[{"Caption":"Tiefland Sachsen","Color":-1,"IconActive":false,"IconValue":"","Value":81},{"Caption":"Mittelgebirge Sachsen","Color":-1,"IconActive":false,"IconValue":"","Value":82}]', 'LAYOUT' => 0, 'ICON' => 'location-dot', 'DISPLAY' => 0]],
        90  => ['init' => 91,  'template' => ['PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION, 'OPTIONS' => '[{"Caption":"Nordhessen und hess. Mittelgebirge","Color":-1,"IconActive":false,"IconValue":"","Value":91},{"Caption":"Rhein-Main","Color":-1,"IconActive":false,"IconValue":"","Value":92}]', 'LAYOUT' => 0, 'ICON' => 'location-dot', 'DISPLAY' => 0]],
        100 => ['init' => 103, 'template' => ['PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION, 'OPTIONS' => '[{"Caption":"Saarland","Color":-1,"IconActive":false,"IconValue":"","Value":103},{"Caption":"Rhein, Pfalz, Nahe und Mosel","Color":-1,"IconActive":false,"IconValue":"","Value":101},{"Caption":"Mittelgebirgsbereich Rheinland-Pfalz","Color":-1,"IconActive":false,"IconValue":"","Value":102}]', 'LAYOUT' => 0, 'ICON' => 'location-dot', 'DISPLAY' => 0]],
        110 => ['init' => 111, 'template' => ['PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION, 'OPTIONS' => '[{"Caption":"Oberrhein und unteres Neckartal","Color":-1,"IconActive":false,"IconValue":"","Value":111},{"Caption":"Hohenlohe/mittlerer Neckar/Oberschwaben","Color":-1,"IconActive":false,"IconValue":"","Value":112},{"Caption":"Mittelgebirge Baden-Württemberg","Color":-1,"IconActive":false,"IconValue":"","Value":113}]', 'LAYOUT' => 0, 'ICON' => 'location-dot', 'DISPLAY' => 0]],
        120 => ['init' => 121, 'template' => ['PRESENTATION' => VARIABLE_PRESENTATION_ENUMERATION, 'OPTIONS' => '[{"Caption":"Allgäu/Oberbayern/Bay. Wald","Color":-1,"IconActive":false,"IconValue":"","Value":121},{"Caption":"Donauniederungen","Color":-1,"IconActive":false,"IconValue":"","Value":122},{"Caption":"Bayern n. der Donau, o. Bayr. Wald, o. Mainfranken","Color":-1,"IconActive":false,"IconValue":"","Value":123},{"Caption":"Mainfranken","Color":-1,"IconActive":false,"IconValue":"","Value":124}]', 'LAYOUT' => 0, 'ICON' => 'location-dot', 'DISPLAY' => 0]],
    ];

    /**
     * @var array<string,mixed> Days presentation for the visualization
     */
    private const POLLEN_PRESENTATION_DAYS = [
        'PRESENTATION'        => VARIABLE_PRESENTATION_SLIDER,
        'USAGE_TYPE'          => 5,
        'THOUSANDS_SEPARATOR' => '',
        'DECIMAL_SEPARATOR'   => 'Client',
        'PERCENTAGE'          => false,
        'DIGITS'              => 0,
        'INTERVALS'           => '[]',
        'ICON'                => 'Calendar',
        'INTERVALS_ACTIVE'    => false,
        'MAX'                 => 3,
        'GRADIENT_TYPE'       => 0,
        'MIN'                 => 1,
        'CUSTOM_GRADIENT'     => '[]',
        'PREFIX'              => '',
        'STEP_SIZE'           => 1.0,
        'SUFFIX'              => '',
    ];

    /**
     * @var array<string,mixed> Hint presentation for the visualization
     */
    private const POLLEN_PRESENTATION_HINT = [
        'PRESENTATION'        => VARIABLE_PRESENTATION_VALUE_PRESENTATION,
        'DISPLAY_TYPE'        => 0,
        'USAGE_TYPE'          => 0,
        'THOUSANDS_SEPARATOR' => '',
        'SHOW_PREVIEW'        => true,
        'SUFFIX'              => '',
        'COLOR'               => -1,
        'MAX'                 => 100,
        'MULTILINE'           => false,
        'DECIMAL_SEPARATOR'   => 'Client',
        'PERCENTAGE'          => false,
        'DIGITS'              => 2,
        'INTERVALS'           => '[]',
        'ICON'                => 'message-dots',
        'INTERVALS_ACTIVE'    => false,
        'PREVIEW_STYLE'       => 1,
        'MIN'                 => 0,
        'OPTIONS'             => '[]',
        'CONTENT_COLOR'       => -1,
        'PREFIX'              => '',
    ];

    /**
     * In contrast to Construct, this function is called only once when creating the instance and starting IP-Symcon.
     * Therefore, status variables and module properties which the module requires permanently should be created here.
     *
     * @return void
     */
    public function Create(): void
    {
        //Never delete this line!
        parent::Create();

        // Pollen Risk Index
        $this->RegisterAttributeString('IndexInfo', '[]');

        // Pollen calendar media
        $this->RegisterPropertyBoolean('MediaAll', false);
        $this->RegisterPropertyBoolean('MediaNorth', false);
        $this->RegisterPropertyBoolean('MediaEast', false);
        $this->RegisterPropertyBoolean('MediaSouth', false);
        $this->RegisterPropertyBoolean('MediaWest', false);

        // Visualisation
        $this->RegisterPropertyBoolean('ShowRagweed', true);
        $this->RegisterPropertyBoolean('ShowMugwort', true);
        $this->RegisterPropertyBoolean('ShowBirch', true);
        $this->RegisterPropertyBoolean('ShowAlder', true);
        $this->RegisterPropertyBoolean('ShowAsh', true);
        $this->RegisterPropertyBoolean('ShowGrasses', true);
        $this->RegisterPropertyBoolean('ShowHazel', true);
        $this->RegisterPropertyBoolean('ShowRye', true);

        // Notifications
        $this->RegisterPropertyInteger('DashboardMessage', 0);
        $this->RegisterPropertyInteger('DashboardLevel', 4);
        $this->RegisterPropertyInteger('NotificationMessage', 0);
        $this->RegisterPropertyInteger('NotificationLevel', 4);
        $this->RegisterPropertyInteger('InstanceVisualization', 1);
        $this->RegisterPropertyInteger('ScriptMessage', 1);

        // Settings
        $this->RegisterPropertyBoolean('CreateHint', true);

        // Register daily update timer
        $this->RegisterTimer('PollenUpdate', 0, 'POLLEN_Update(' . $this->InstanceID . ');');

        // Set visualization type to 1, as we want to offer HTML
        $this->SetVisualizationType(1);
    }

    /**
     * This function is called when deleting the instance during operation and when updating via "Module Control".
     * The function is not called when exiting IP-Symcon.
     *
     * @return void
     */
    public function Destroy(): void
    {
        // Never delete this line!
        parent::Destroy();
    }

    /**
     * The content can be overwritten in order to transfer a self-created configuration page.
     * This way, content can be generated dynamically.
     * In this case, the "form.json" on the file system is completely ignored.
     *
     * @return string Content of the configuration page.
     */
    public function GetConfigurationForm(): string
    {
        // first we read the preperated form data
        $form = json_decode(file_get_contents(__DIR__ . '/form.json'), true);

        // Extract Version
        $ins = IPS_GetInstance($this->InstanceID);
        $mod = IPS_GetModule($ins['ModuleInfo']['ModuleID']);
        $lib = IPS_GetLibrary($mod['LibraryID']);
        $form['actions'][2]['items'][2]['caption'] = sprintf('v%s.%d', $lib['Version'], $lib['Build']);

        return json_encode($form);
    }

    /**
     * Is executed when "Apply" is pressed on the configuration page and immediately after the instance has been created.
     *
     * @return void
     */
    public function ApplyChanges(): void
    {
        //Never delete this line!
        parent::ApplyChanges();

        // Images
        foreach (self::JPGS as $jpg) {
            $img = $this->ReadPropertyBoolean($jpg[0]);
            if ($img) {
                $mid = $this->CreateMediaImage($jpg[0], $this->Translate($jpg[1]), $jpg[2], 'jpg', false);
                $contents = file_get_contents($jpg[3]);
                IPS_SetMediaContent($mid, base64_encode($contents));
                IPS_SendMediaEvent($mid);
            } else {
                $mid = @$this->GetIDForIdent($jpg[0]);
                if ($mid) {
                    IPS_DeleteMedia($mid, true);
                }
            }
        }

        // Update Variables
        $ret = $this->RegisterVariableInteger('LastUpdate', $this->Translate('Last update'), self::POLLEN_PRESENTATION_DT, 1);
        if ($ret) {
            IPS_SetIcon($this->GetIDForIdent('LastUpdate'), 'Calendar');
        }
        $ret = $this->RegisterVariableInteger('NextUpdate', $this->Translate('Next update'), self::POLLEN_PRESENTATION_DT, 2);
        if ($ret) {
            IPS_SetIcon($this->GetIDForIdent('NextUpdate'), 'Calendar');
        }

        // State,region and days + Initial state
        $state = 10;
        $ret = $this->RegisterVariableInteger('State', $this->Translate('State'), self::POLLEN_PRESENTATION_STATE, 3);
        if ($ret) {
            $this->SetValueInteger('State', $state);
        } else {
            $state = $this->GetValue('State');
        }
        $ret = $this->RegisterVariableInteger('Region', $this->Translate('Region'), self::POLLEN_PRESENTATION_REGIONS[$state]['template'], 4);
        if ($ret) {
            $this->SetValueInteger('Region', self::POLLEN_PRESENTATION_REGIONS[$state]['init']);
        }
        $ret = $this->RegisterVariableInteger('Days', $this->Translate('Days'), self::POLLEN_PRESENTATION_DAYS, 5);
        if ($ret) {
            $this->SetValueInteger('Days', 2);
        }
        // Enable actions for variables
        $this->EnableAction('State');
        $this->EnableAction('Region');
        $this->EnableAction('Days');

        // Daily hint
        $hint = $this->ReadPropertyBoolean('CreateHint');
        $this->MaintainVariable('Hint', $this->Translate('Tageshinweis'), VARIABLETYPE_STRING, self::POLLEN_PRESENTATION_HINT, 6, $hint);

        // DWD renew data always 11 o'clock
        $this->UpdateTimerInterval('PollenUpdate', 11, 15, 0);

        // Update visualization
        $this->UpdateVisualizationValue($this->GetFullUpdateMessage());
    }

    /**
     * Is called when, for example, a button is clicked in the visualization.
     *
     * @param string $ident Ident of the variable
     * @param mixed $value The value to be set
     * @return void
     */
    public function RequestAction(string $ident, mixed $value): void
    {
        // Debug output
        $this->LogDebug('RequestAction', $ident . ' => ' . $value);
        switch ($ident) {
            case 'State':
                $this->SetValueInteger($ident, $value);
                $this->RegisterVariableInteger('Region', $this->Translate('Region'), self::POLLEN_PRESENTATION_REGIONS[$value]['template'], 4);
                // select the always the first
                $this->SetValueInteger('Region', self::POLLEN_PRESENTATION_REGIONS[$value]['init']);
                $this->Update();
                break;
            case 'Region':
                $this->SetValueInteger($ident, $value);
                $this->Update();
                break;
            case 'Days':
                $this->SetValueInteger($ident, $value);
                break;
        }
        $this->UpdateVisualizationValue($this->GetFullUpdateMessage());
    }

    /**
     * If the HTML-SDK is to be used, this function must be overwritten in order to return the HTML content.
     *
     * @return string Initial display of a representation via HTML SDK
     */
    public function GetVisualizationTile(): string
    {
        // Add a script to set the values when loading, analogous to changes at runtime
        // Although the return from GetFullUpdateMessage is already JSON-encoded, json_encode is still executed a second time
        // This adds quotation marks to the string and any quotation marks within it are escaped correctly
        $initialHandling = '<script>handleMessage(' . json_encode($this->GetFullUpdateMessage()) . ');</script>';
        // Add static HTML from file
        $module = file_get_contents(__DIR__ . '/module.html');
        // Return everything
        // Important: $initialHandling at the end, as the handleMessage function is only defined in the HTML
        return $module . $initialHandling;
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * POLLEN_Update($id);
     *
     * @return void
     */
    public function Update(): void
    {
        // Get index info
        $json = $this->IndexInfo();
        $data = json_decode($json, true);
        // Last Update
        if (array_key_exists('last', $data)) {
            $this->SetValueInteger('LastUpdate', $data['last']);
        }
        // Next Update
        if (array_key_exists('next', $data)) {
            $this->SetValueInteger('NextUpdate', $data['next']);
        }

        if (array_key_exists('index', $data)) {
            // analyse index
            $info = $this->BuildText($data['index']);

            // Daily hint ?
            $hint = $this->ReadPropertyBoolean('CreateHint');
            if ($hint) {
                $this->SetValueString('Hint', $info['TEXT']);
            }

            // Messages
            $dashboard = $this->ReadPropertyInteger('DashboardMessage');
            $notify = $this->ReadPropertyInteger('NotificationMessage');
            $script = $this->ReadPropertyInteger('ScriptMessage');
            $visu = $this->ReadPropertyInteger('InstanceVisualization');

            // send to dashboard
            if ($dashboard && $script >= self::IPS_MIN_ID) {
                if ($info['LEVEL'] >= $this->ReadPropertyInteger('DashboardLevel')) {
                    $msg = IPS_RunScriptWaitEx($script, ['action' => 'add', 'text' => $info['TEXT'], 'removable' => true, 'type' => 2, 'image' => 'wheat-awn-circle-exclamation']);
                }
            }
            // send to visualization
            if ($notify && $visu >= self::IPS_MIN_ID) {
                if ($info['LEVEL'] >= $this->ReadPropertyInteger('NotificationLevel')) {
                    if ($this->IsWebFrontVisuInstance($visu)) {
                        WFC_PushNotification($visu, $this->Translate('Pollen Count'), $info['TEXT'], 'Snow', 0);
                    }
                    if ($this->IsTileVisuInstance($visu)) {
                        VISU_PostNotificationEx($visu, $this->Translate('Pollen Count'), $info['TEXT'], 'wheat-awn-circle-exclamation', 'space', $this->InstanceID);
                    }
                }
            }

            // Save index info for visualization
            $this->WriteAttributeString('IndexInfo', $json);
            $this->UpdateVisualizationValue($this->GetFullUpdateMessage());
        }
        // calculate next update interval
        $this->UpdateTimerInterval('PollenUpdate', 11, 15, 0);
    }

    /**
     * This function will be available automatically after the module is imported with the module control.
     * Using the custom prefix this function will be callable from PHP and JSON-RPC through:.
     *
     * POLLEN_IndexInfo($id);
     *
     * @return string JSON encoded array with index information, last and next update time.
     */
    public function IndexInfo(): string
    {
        // Output array
        $index = [];
        // Data source
        $json = file_get_contents(self::JSON);
        // Safty check
        if (empty($json)) {
            $this->LogMessage($this->Translate('Error while reading the DWD pollen danger index!'), KL_ERROR);
        } else {
            $data = json_decode($json, true);
            // Last Update
            if (array_key_exists('last_update', $data)) {
                $update = str_replace(' Uhr', '', $data['last_update']);
                $last = strtotime($update);
                $index['last'] = $last;
            } else {
                $this->LogMessage($this->Translate('Error reading the last update!'), KL_WARNING);
            }
            // Next Update
            if (array_key_exists('next_update', $data)) {
                $update = str_replace(' Uhr', '', $data['next_update']);
                $next = strtotime($update);
                $index['next'] = $next;
            } else {
                $this->LogMessage($this->Translate('Error reading the next update!'), KL_WARNING);
            }
            // Collect index data
            $state = $this->GetValue('State');
            $region = $this->GetValue('Region');
            // search
            foreach ($data['content'] as $content) {
                if (($content['region_id'] == $state) && ($content['partregion_id'] == $region)) {
                    $pollen = $content['Pollen'];
                    // Neues Array mit meinen Pollenflugdaten aufbauen
                    $pollination = [];
                    foreach ($pollen as $key => $value) {
                        $pollination[$key] = [
                            self::LEVEL[$value['today']],
                            self::LEVEL[$value['tomorrow']],
                            self::LEVEL[$value['dayafter_to']],
                        ];
                    }
                    // sort by key
                    ksort($pollination);
                    // save index data
                    $index['index'] = $pollination;
                    break;
                }
            }
        }
        // dump result
        $this->LogDebug('DATA: ', $index);
        // return date info as json
        return json_encode($index);
    }

    /**
     * Generate a message that updates all elements in the HTML display.
     *
     * @return string JSON encoded message information
     */
    private function GetFullUpdateMessage(): string
    {
        $result = [];

        $json = $this->ReadAttributeString('IndexInfo');
        $data = json_decode($json, true);

        if (isset($data['index'])) {
            $index = $data['index'];

            $days = $this->GetValue('Days');
            // Clean up days
            foreach ($index as $key => &$value) {
                if ($days == 1) {
                    unset($value[1], $value[2]);
                } elseif ($days == 2) {
                    unset($value[2]);
                }
            }
            unset($value);

            // Clean up pollen
            foreach (self::POLLEN as $key => $value) {
                if (!$this->ReadPropertyBoolean($key)) {
                    unset($index[$value]);
                }
            }

            // Data
            $result = [
                'update' => $data['last'] ?? 0,
                'index'  => $index
            ];
        } else {
            $this->LogDebug(__FUNCTION__, 'No index data available');
        }

        return json_encode($result);
    }

    /**
     * This function creats the textual summary of the forecast.
     *
     * @param array<string,mixed> $pollination Aarray of pollen count dates.
     * @return array<string,mixed> Array with text and maximum level.
     */
    private function BuildText(array $pollination): array
    {
        // Vorhersage für geringe Belastung
        $gb_text = '';
        // Vorhersage für mittlere Belastung
        $mb_text = '';
        // Vorhersage für hohe Belastung
        $hb_text = '';
        // Maximale Belastung
        $max = 0;
        // Belastung durch ???
        foreach ($pollination as $key => $value) {
            if ($value[0] > $max) {
                $max = $value[0];
            }
            switch ($value[0]) {
                case '1':
                case '2':
                    $gb_text = $gb_text . $key . ' ';
                    break;
                case '3':
                case '4':
                    $mb_text = $mb_text . $key . ' ';
                    break;
                case '5':
                case '6':
                    $hb_text = $hb_text . $key . ' ';
                    break;
            }
        }
        // Ansagetext zusammen stellen
        if ($gb_text !== '') {
            $gb_text = 'Geringe Belastung durch ' . str_replace(' ', ', ', trim($gb_text) . '.');
        }
        if ($mb_text !== '') {
            $mb_text = 'Mittlere Belastung durch ' . str_replace(' ', ', ', trim($mb_text) . '.');
        }
        if ($hb_text !== '') {
            $hb_text = 'Hohe Belastung durch ' . str_replace(' ', ', ', trim($hb_text) . '.');
        }
        $text = '';
        if ($gb_text !== '') {
            $text = trim($gb_text) . ' ';
        }
        if ($mb_text !== '') {
            $text = $text . trim($mb_text) . ' ';
        }
        if ($hb_text !== '') {
            $text = $text . trim($hb_text) . ' ';
        }
        // Nix los
        if ($text == '') {
            $text = 'Keine Belastung.';
        }
        return ['TEXT' => $text, 'LEVEL' => $max];
    }

    /**
     * Create a media variable to take over the snapshots.
     *
     * @param string   $ident      Ident.
     * @param string   $name       Name
     * @param string   $filename   Image file name.
     * @param string   $fileext    Image file extension.
     * @param bool     $cache      Use In-memory cache
     *
     * @return int Media ID
     */
    private function CreateMediaImage(string $ident, string $name, string $filename, string $fileext = 'jpg', bool $cache = true): int
    {
        $file = IPS_GetKernelDir() . 'media' . DIRECTORY_SEPARATOR . $filename . '.' . $fileext;  // Image-Datei

        $mediaID = @$this->GetIDForIdent($ident);
        if (!IPS_MediaExists($mediaID)) {
            $mediaID = IPS_CreateMedia(1);
            IPS_SetParent($mediaID, $this->InstanceID);
            IPS_SetIdent($mediaID, $ident);
            IPS_SetMediaCached($mediaID, $cache);
            // Connect to file
            IPS_SetMediaFile($mediaID, $file, false);
            IPS_SetName($mediaID, $name);
            // Transparent png 1x1 Base64
            // $content = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
            // Set Content
            //IPS_SetMediaContent($mediaID, $content);
            // Update
            //IPS_SendMediaEvent($mediaID);
        }
        return $mediaID;
    }
}
