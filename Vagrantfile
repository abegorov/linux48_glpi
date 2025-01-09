# -*- mode: ruby -*-
# vi: set ft=ruby :

DEFAULT_MACHINE = {
  :domain => 'internal',
  :box => 'rockylinux/9/v9.5-20241118.0',
  :cpus => 2,
  :memory => 2048,
  :networks => {},
  :intnets => {},
  :forwarded_ports => [],
  :modifyvm => []
}

MACHINES = {
  :backup   => { :intnets => { :lan => { ip: '192.168.57.10' } } },
  :primary  => { :intnets => { :lan => { ip: '192.168.57.11' } } },
  :replica  => { :intnets => { :lan => { ip: '192.168.57.12' } } },
  :nfs      => { :intnets => { :lan => { ip: '192.168.57.13' } } },
  :backend1 => {
    :intnets => { :lan => { ip: '192.168.57.14' } },
    :forwarded_ports => [
      { :id => 'https', :guest_ip => '192.168.57.14', :guest => 443,
                        :host_ip  =>     '127.0.0.1', :host  => 8444 },
      { :id => 'vts',   :guest_ip => '192.168.57.14', :guest => 9913,
                        :host_ip  =>     '127.0.0.1', :host  => 9901 },
    ],
  },
  :backend2 => {
    :intnets => { :lan => { ip: '192.168.57.15' } },
    :forwarded_ports => [
      { :id => 'https', :guest_ip => '192.168.57.15', :guest => 443,
                        :host_ip  =>     '127.0.0.1', :host  => 8445 },
      { :id => 'vts',   :guest_ip => '192.168.57.15', :guest => 9913,
                        :host_ip  =>     '127.0.0.1', :host  => 9902 },
    ],
  },
  :logs => {
    :cpus => 2,
    :memory => 4096,
    :intnets => { :lan => { ip: '192.168.57.16' } },
    :forwarded_ports => [
      { :id => 'kibana', :guest_ip => '192.168.57.16', :guest => 5601,
                         :host_ip  =>     '127.0.0.1', :host  => 5601 },
    ],
  },
  :monitor => {
    :cpus => 2,
    :memory => 4096,
    :intnets => { :lan => { ip: '192.168.57.17' } },
    :forwarded_ports => [
      { :id => 'grafana', :guest_ip => '192.168.57.17', :guest => 3000,
                          :host_ip  =>     '127.0.0.1', :host  => 3000 },
      { :id => 'prom',    :guest_ip => '192.168.57.17', :guest => 9090,
                          :host_ip  =>     '127.0.0.1', :host  => 9090 },
      { :id => 'alert',   :guest_ip => '192.168.57.17', :guest => 9093,
                          :host_ip  =>     '127.0.0.1', :host  => 9093 },
    ],
  },
  :frontend => {
    :intnets => { :lan => { ip: '192.168.57.18' } },
    :networks => { :private_network => { ip: '192.168.56.18' } },
    :forwarded_ports => [
      { :id => 'vts',   :guest_ip => '192.168.57.18', :guest => 9913,
                        :host_ip  =>     '127.0.0.1', :host  => 9900 },
    ],
  },
}

ANSIBLE_GROUPS = {
  'mariadb' => ['primary', 'replica'],
  'backend' => ['backend1', 'backend2'],
}

ENV['ANSIBLE_STDOUT_CALLBACK'] = 'yaml'

def provisioned?(host_name)
  return File.exist?('.vagrant/machines/' + host_name.to_s +
    '/virtualbox/action_provision')
end

Vagrant.configure('2') do |config|
  MACHINES.each do |host_name, host_config|
    host_config = DEFAULT_MACHINE.merge(host_config)
    config.vm.define host_name do |host|
      host.vm.box = host_config[:box]
      if not provisioned?(host_name)
        host.vm.host_name = host_name.to_s + '.' + host_config[:domain].to_s
      end

      host.vm.provider :virtualbox do |vb|
        vb.cpus = host_config[:cpus]
        vb.memory = host_config[:memory]

        if !host_config[:modifyvm].empty?
          vb.customize ['modifyvm', :id] + host_config[:modifyvm]
        end
      end

      host_config[:intnets].each do |name, intnet|
        intnet[:virtualbox__intnet] = name.to_s
        host.vm.network(:private_network, **intnet)
      end
      host_config[:networks].each do |network_type, network_args|
        host.vm.network(network_type, **network_args)
      end
      host_config[:forwarded_ports].each do |forwarded_port|
        host.vm.network(:forwarded_port, **forwarded_port)
      end

      if MACHINES.keys.last == host_name
        host.vm.provision :ansible do |ansible|
          ansible.playbook = 'playbook.yml'
          ansible.groups = ANSIBLE_GROUPS
          ansible.limit = 'all'
          ansible.compatibility_mode = '2.0'
          ansible.raw_arguments = ['--diff']
          ansible.tags = 'all'
        end
      end

      host.vm.synced_folder '.', '/vagrant', disabled: true
    end
  end
end
